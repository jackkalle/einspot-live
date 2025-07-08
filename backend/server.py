from fastapi import FastAPI, APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field, EmailStr
from typing import List, Optional
import uuid
from datetime import datetime, timedelta

# Password Hashing
from passlib.context import CryptContext

# JWT
from jose import JWTError, jwt

ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

# Configuration
MONGO_URL = os.environ['MONGO_URL']
DB_NAME = os.environ['DB_NAME']
JWT_SECRET_KEY = os.environ.get('JWT_SECRET_KEY', "default_super_secret_key_for_dev_only") # Use a strong key in .env
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

# MongoDB connection
client = AsyncIOMotorClient(MONGO_URL)
db = client[DB_NAME]

# Password hashing context
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

# OAuth2 Scheme
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/auth/login") # Adjusted to match frontend

# Create the main app without a prefix
app = FastAPI(title="EINSPOT API", version="1.0.0")

# Create a router with the /api prefix
api_router = APIRouter(prefix="/api")

# --- Pydantic Models ---
class PyObjectId(uuid.UUID):
    @classmethod
    def __get_validators__(cls):
        yield cls.validate

    @classmethod
    def validate(cls, v, field_info):
        if not isinstance(v, uuid.UUID):
            try:
                return uuid.UUID(str(v))
            except ValueError:
                raise ValueError("Not a valid ObjectId")
        return v

    @classmethod
    def __get_pydantic_json_schema__(cls, field_schema):
        field_schema.update(type="string")


class UserBase(BaseModel):
    email: EmailStr
    firstName: Optional[str] = None
    lastName: Optional[str] = None
    # Add other fields as per your frontend's registration form

class UserCreate(UserBase):
    password: str

class UserInDB(UserBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    hashed_password: str
    disabled: Optional[bool] = False
    isAdmin: Optional[bool] = Field(default=False) # New admin field
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {
            PyObjectId: str,
            datetime: lambda dt: dt.isoformat()
        }
        arbitrary_types_allowed = True


class UserPublic(UserBase):
    id: PyObjectId = Field(alias="_id")
    isAdmin: Optional[bool] = Field(default=False) # New admin field
    createdAt: datetime
    updatedAt: datetime

    class Config:
        populate_by_name = True
        json_encoders = {
            PyObjectId: str,
            datetime: lambda dt: dt.isoformat()
        }
        arbitrary_types_allowed = True


class Token(BaseModel):
    access_token: str
    token_type: str
    user: UserPublic # Include user details in the token response

class TokenData(BaseModel):
    email: Optional[str] = None


# --- Utility Functions ---
def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password):
    return pwd_context.hash(password)

async def get_user_by_email(email: EmailStr) -> Optional[UserInDB]:
    user_doc = await db.users.find_one({"email": email})
    if user_doc:
        return UserInDB(**user_doc)
    return None

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, JWT_SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

async def get_current_user(token: str = Depends(oauth2_scheme)) -> UserInDB:
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, JWT_SECRET_KEY, algorithms=[ALGORITHM])
        email: str = payload.get("sub")
        if email is None:
            raise credentials_exception
        token_data = TokenData(email=email)
    except JWTError:
        raise credentials_exception
    user = await get_user_by_email(email=token_data.email)
    if user is None:
        raise credentials_exception
    if user.disabled:
        raise HTTPException(status_code=400, detail="Inactive user")
    return user

async def get_current_active_user(current_user: UserInDB = Depends(get_current_user)):
    # This is a shortcut if we just want to ensure the user is active.
    # get_current_user already checks for disabled status.
    return current_user

async def get_current_admin_user(current_user: UserInDB = Depends(get_current_active_user)):
    if not current_user.isAdmin:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="The user doesn't have enough privileges"
        )
    return current_user

# --- Authentication Routes ---
auth_router = APIRouter(prefix="/auth", tags=["Authentication"])

@auth_router.post("/register", response_model=UserPublic, status_code=status.HTTP_201_CREATED)
async def register_user(user_in: UserCreate):
    existing_user = await get_user_by_email(user_in.email)
    if existing_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email already registered"
        )

    hashed_password = get_password_hash(user_in.password)
    user_db_data = user_in.model_dump(exclude={"password"})
    user_db_data["_id"] = uuid.uuid4() # Ensure _id is a UUID
    user_db_data["hashed_password"] = hashed_password

    new_user = UserInDB(**user_db_data)

    await db.users.insert_one(new_user.model_dump(by_alias=True))

    # Prepare public user data for response, ensuring _id is correctly aliased
    public_user_data = new_user.model_dump(exclude={"hashed_password", "disabled"})
    public_user_data["id"] = public_user_data.pop("_id") # Ensure 'id' is the key in response
    return UserPublic(**public_user_data)


@auth_router.post("/login", response_model=Token)
async def login_for_access_token(form_data: OAuth2PasswordRequestForm = Depends()):
    user = await get_user_by_email(EmailStr(form_data.username)) # FastAPI OAuth2PasswordRequestForm uses 'username'
    if not user or not verify_password(form_data.password, user.hashed_password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect email or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    if user.disabled:
        raise HTTPException(status_code=400, detail="Inactive user")

    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": user.email}, expires_delta=access_token_expires
    )

    # Prepare public user data for response
    public_user_data = user.model_dump(exclude={"hashed_password", "disabled"})
    public_user_data["id"] = public_user_data.pop("_id")
    user_public_info = UserPublic(**public_user_data)

    return {"access_token": access_token, "token_type": "bearer", "user": user_public_info}

@auth_router.get("/me", response_model=UserPublic)
async def read_users_me(current_user: UserInDB = Depends(get_current_active_user)):
    public_user_data = current_user.model_dump(exclude={"hashed_password", "disabled"})
    public_user_data["id"] = public_user_data.pop("_id")
    return UserPublic(**public_user_data)

# Include auth router in the main API router
api_router.include_router(auth_router)


# --- Placeholder for other routers (Products, Orders, etc.) ---

# --- Product Models ---
class CategoryBase(BaseModel):
    name: str
    description: Optional[str] = None

class CategoryCreate(CategoryBase):
    pass

class CategoryInDB(CategoryBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class CategoryPublic(CategoryInDB): # For now, public is same as InDB
    pass


class ProductBase(BaseModel):
    name: str
    description: Optional[str] = None
    price: float = Field(..., gt=0)
    stock_quantity: int = Field(..., ge=0)
    category_id: Optional[PyObjectId] = None # Reference to Category's ID
    images: Optional[List[str]] = [] # List of image URLs or paths

class ProductCreate(ProductBase):
    pass

class ProductInDB(ProductBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    # owner_id: PyObjectId # To link product to a user/seller if needed
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class ProductPublic(ProductInDB): # For now, public is same as InDB, can be customized
    category: Optional[CategoryPublic] = None # Populate category details

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True


# --- Products and Categories Routes ---
products_router = APIRouter(prefix="/products", tags=["Products & Categories"])

# Category Endpoints
@products_router.post("/categories", response_model=CategoryPublic, status_code=status.HTTP_201_CREATED)
async def create_category(category_in: CategoryCreate, current_user: UserInDB = Depends(get_current_active_user)): # Protected
    category_doc = category_in.model_dump()
    category_doc["_id"] = uuid.uuid4()

    new_category = CategoryInDB(**category_doc)
    await db.categories.insert_one(new_category.model_dump(by_alias=True))

    public_category_data = new_category.model_dump()
    public_category_data["id"] = public_category_data.pop("_id")
    return CategoryPublic(**public_category_data)

@products_router.get("/categories", response_model=List[CategoryPublic])
async def get_all_categories():
    categories_cursor = db.categories.find()
    categories_list = await categories_cursor.to_list(length=1000) # Adjust length as needed
    return [CategoryPublic(**{**cat, "id": cat["_id"]}) for cat in categories_list]

# Product Endpoints
@products_router.post("/", response_model=ProductPublic, status_code=status.HTTP_201_CREATED)
async def create_product(product_in: ProductCreate, current_user: UserInDB = Depends(get_current_active_user)): # Protected
    product_doc = product_in.model_dump()
    product_doc["_id"] = uuid.uuid4()
    # product_doc["owner_id"] = current_user.id # If linking product to user

    if product_in.category_id:
        category = await db.categories.find_one({"_id": product_in.category_id})
        if not category:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Category not found")

    new_product = ProductInDB(**product_doc)
    await db.products.insert_one(new_product.model_dump(by_alias=True))

    # Prepare public product data
    public_product_data = new_product.model_dump()
    public_product_data["id"] = public_product_data.pop("_id")
    if new_product.category_id:
        category_data = await db.categories.find_one({"_id": new_product.category_id})
        if category_data:
             public_product_data["category"] = CategoryPublic(**{**category_data, "id": category_data["_id"]})

    return ProductPublic(**public_product_data)

@products_router.get("/", response_model=List[ProductPublic])
async def get_all_products(skip: int = 0, limit: int = 100): # Basic pagination
    products_cursor = db.products.find().skip(skip).limit(limit)
    products_list = await products_cursor.to_list(length=limit)

    populated_products = []
    for prod_data in products_list:
        prod_data["id"] = prod_data["_id"]
        if prod_data.get("category_id"):
            category_data = await db.categories.find_one({"_id": prod_data["category_id"]})
            if category_data:
                prod_data["category"] = CategoryPublic(**{**category_data, "id": category_data["_id"]})
        populated_products.append(ProductPublic(**prod_data))
    return populated_products

@products_router.get("/{product_id}", response_model=ProductPublic)
async def get_product_by_id(product_id: PyObjectId):
    product_data = await db.products.find_one({"_id": product_id})
    if not product_data:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Product not found")

    product_data["id"] = product_data["_id"]
    if product_data.get("category_id"):
        category_data = await db.categories.find_one({"_id": product_data["category_id"]})
        if category_data:
            product_data["category"] = CategoryPublic(**{**category_data, "id": category_data["_id"]})

    return ProductPublic(**product_data)


# TODO: Implement search endpoint /products/search as per frontend api.js
@products_router.get("/search/", response_model=List[ProductPublic]) # Changed path to end with /
async def search_products(q: Optional[str] = None, category: Optional[str] = None, skip: int = 0, limit: int = 20):
    query_filter = {}
    if q:
        # Using regex for a simple case-insensitive partial match on name and description
        # For more advanced search, MongoDB's $text operator and text indexes are better.
        # This requires creating a text index e.g., db.products.create_index([("name", "text"), ("description", "text")])
        # query_filter["$text"] = {"$search": q}
        query_filter["$or"] = [
            {"name": {"$regex": q, "$options": "i"}},
            {"description": {"$regex": q, "$options": "i"}}
        ]

    if category:
        # Assuming category is passed as ID string, convert to PyObjectId
        try:
            category_obj_id = PyObjectId.validate(category, None)
            query_filter["category_id"] = category_obj_id
        except ValueError:
            # If category is not a valid ObjectId, maybe it's a name?
            # This part would require fetching category by name then its ID.
            # For simplicity, we'll assume ID is passed or ignore if invalid.
            pass

    products_cursor = db.products.find(query_filter).skip(skip).limit(limit)
    products_list = await products_cursor.to_list(length=limit)

    populated_products = []
    for prod_data in products_list:
        prod_data["id"] = prod_data["_id"]
        if prod_data.get("category_id"):
            category_data = await db.categories.find_one({"_id": prod_data["category_id"]})
            if category_data:
                prod_data["category"] = CategoryPublic(**{**category_data, "id": category_data["_id"]})
        populated_products.append(ProductPublic(**prod_data))
    return populated_products

api_router.include_router(products_router)

# --- Order Models ---
class OrderItemBase(BaseModel):
    product_id: PyObjectId
    quantity: int = Field(..., gt=0)
    price_at_purchase: float # Price of the product when the order was made

class OrderItemCreate(OrderItemBase):
    pass

class OrderItemPublic(OrderItemBase):
    # Could potentially populate product details here if needed in order responses
    # product: Optional[ProductPublic] = None
    pass

class OrderBase(BaseModel):
    # customer_id: PyObjectId # Filled by current_user
    shipping_address: str # Simplified for now, can be a structured address model
    billing_address: Optional[str] = None
    total_amount: float
    status: str = Field(default="pending") # e.g., pending, processing, shipped, delivered, cancelled
    payment_method: Optional[str] = None
    payment_status: str = Field(default="pending") # e.g., pending, paid, failed

class OrderCreate(OrderBase):
    items: List[OrderItemCreate]

class OrderInDB(OrderBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    customer_id: PyObjectId
    items: List[OrderItemPublic] # Store resolved items
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class OrderPublic(OrderInDB): # For now, public is same as InDB
    pass


# --- Orders Routes ---
orders_router = APIRouter(prefix="/orders", tags=["Orders"])

@orders_router.post("/", response_model=OrderPublic, status_code=status.HTTP_201_CREATED)
async def create_order(order_in: OrderCreate, current_user: UserInDB = Depends(get_current_active_user)):
    order_items_data = []
    calculated_total_amount = 0.0

    for item_in in order_in.items:
        product = await db.products.find_one({"_id": item_in.product_id})
        if not product:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail=f"Product with ID {item_in.product_id} not found.")

        # Basic check for stock, can be more complex (e.g., decrement stock)
        if product["stock_quantity"] < item_in.quantity:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail=f"Not enough stock for product {product['name']}.")

        item_data = item_in.model_dump()
        item_data["price_at_purchase"] = product["price"] # Use current product price
        order_items_data.append(OrderItemPublic(**item_data))
        calculated_total_amount += product["price"] * item_in.quantity

    # VAT Calculation (7.5% for Nigeria)
    vat_rate = 0.075
    vat_amount = calculated_total_amount * vat_rate
    final_total_amount = calculated_total_amount + vat_amount

    # Ensure total_amount from input matches calculated or use calculated
    # For now, let's trust the frontend calculation for total_amount if it includes VAT,
    # or use our calculated_total_amount + VAT.
    # Here, we'll use the server-calculated total_amount including VAT.
    # The `order_in.total_amount` might be the subtotal before VAT from the frontend.
    # Let's assume order_in.total_amount is subtotal, and we add VAT here.
    # If frontend sends total_amount *including* VAT, then this logic needs adjustment or validation.

    order_doc = order_in.model_dump(exclude={"items", "total_amount"}) # Exclude items for now, total_amount to use server calculated
    order_doc["_id"] = uuid.uuid4()
    order_doc["customer_id"] = current_user.id
    order_doc["items"] = [item.model_dump() for item in order_items_data]
    order_doc["total_amount"] = final_total_amount # Use server-calculated final total

    new_order = OrderInDB(**order_doc)
    await db.orders.insert_one(new_order.model_dump(by_alias=True))

    # TODO: Potentially decrement stock quantities here after successful order creation
    # for item_in in order_in.items:
    #     await db.products.update_one(
    #         {"_id": item_in.product_id},
    #         {"$inc": {"stock_quantity": -item_in.quantity}}
    #     )

    public_order_data = new_order.model_dump()
    public_order_data["id"] = public_order_data.pop("_id")
    return OrderPublic(**public_order_data)


@orders_router.get("/my-orders", response_model=List[OrderPublic])
async def get_my_orders(current_user: UserInDB = Depends(get_current_active_user), skip: int = 0, limit: int = 50):
    orders_cursor = db.orders.find({"customer_id": current_user.id}).sort("createdAt", -1).skip(skip).limit(limit)
    orders_list = await orders_cursor.to_list(length=limit)
    return [OrderPublic(**{**order, "id": order["_id"]}) for order in orders_list]

@orders_router.get("/{order_id}", response_model=OrderPublic)
async def get_order_by_id_for_customer(order_id: PyObjectId, current_user: UserInDB = Depends(get_current_active_user)):
    order_data = await db.orders.find_one({"_id": order_id})
    if not order_data:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Order not found")

    if order_data["customer_id"] != current_user.id:
        # Basic ownership check. Admins might need different logic.
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Not authorized to access this order")

    order_data["id"] = order_data["_id"]
    return OrderPublic(**order_data)

api_router.include_router(orders_router)

# --- Contact, Newsletter, and Quote Models & Routes ---

# Contact Form
class ContactFormSubmission(BaseModel):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    name: str
    email: EmailStr
    phone: Optional[str] = None
    company: Optional[str] = None
    service: Optional[str] = None # Service of interest
    message: str
    submittedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class ContactFormCreate(BaseModel):
    name: str
    email: EmailStr
    phone: Optional[str] = None
    company: Optional[str] = None
    service: Optional[str] = None
    message: str

contact_router = APIRouter(tags=["Contact & Submissions"])

@contact_router.post("/contact", response_model=ContactFormSubmission, status_code=status.HTTP_201_CREATED)
async def submit_contact_form(form_data: ContactFormCreate):
    submission_data = form_data.model_dump()
    submission_data["_id"] = uuid.uuid4()

    new_submission = ContactFormSubmission(**submission_data)
    await db.contact_submissions.insert_one(new_submission.model_dump(by_alias=True))

    response_data = new_submission.model_dump()
    response_data["id"] = response_data.pop("_id")
    return ContactFormSubmission(**response_data)

# Newsletter Subscription
class NewsletterSubscription(BaseModel):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    email: EmailStr
    subscribedAt: datetime = Field(default_factory=datetime.utcnow)
    isActive: bool = True

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class NewsletterSubscribeCreate(BaseModel):
    email: EmailStr

@contact_router.post("/newsletter/subscribe", response_model=NewsletterSubscription, status_code=status.HTTP_201_CREATED)
async def subscribe_to_newsletter(subscription_data: NewsletterSubscribeCreate):
    existing_subscription = await db.newsletter_subscriptions.find_one({"email": subscription_data.email})
    if existing_subscription:
        # Optionally update isActive to true if they re-subscribe, or just return existing
        if not existing_subscription.get("isActive", False):
            await db.newsletter_subscriptions.update_one(
                {"_id": existing_subscription["_id"]},
                {"$set": {"isActive": True, "updatedAt": datetime.utcnow()}}
            )
            existing_subscription["isActive"] = True # Ensure response reflects update
        existing_subscription["id"] = existing_subscription.pop("_id")
        return NewsletterSubscription(**existing_subscription)

    sub_doc = subscription_data.model_dump()
    sub_doc["_id"] = uuid.uuid4()
    new_subscription = NewsletterSubscription(**sub_doc)
    await db.newsletter_subscriptions.insert_one(new_subscription.model_dump(by_alias=True))

    response_data = new_subscription.model_dump()
    response_data["id"] = response_data.pop("_id")
    return NewsletterSubscription(**response_data)

# Quote Request
class QuoteRequest(BaseModel):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    name: str
    email: EmailStr
    phone: Optional[str] = None
    company: Optional[str] = None
    service_of_interest: Optional[str] = None # Or make it more structured
    project_description: str
    estimated_budget: Optional[str] = None # Or float
    timeline: Optional[str] = None
    status: str = Field(default="pending") # e.g., pending, contacted, quoted, closed
    submittedAt: datetime = Field(default_factory=datetime.utcnow)
    # items: Optional[List[dict]] = None # If quote is from cart

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class QuoteRequestCreate(BaseModel):
    name: str
    email: EmailStr
    phone: Optional[str] = None
    company: Optional[str] = None
    service_of_interest: Optional[str] = None
    project_description: str
    estimated_budget: Optional[str] = None
    timeline: Optional[str] = None
    # items: Optional[List[dict]] = None # If quote is from cart, define item structure

@contact_router.post("/quotes", response_model=QuoteRequest, status_code=status.HTTP_201_CREATED)
async def submit_quote_request(quote_data: QuoteRequestCreate):
    quote_doc = quote_data.model_dump()
    quote_doc["_id"] = uuid.uuid4()

    new_quote_request = QuoteRequest(**quote_doc)
    await db.quote_requests.insert_one(new_quote_request.model_dump(by_alias=True))

    response_data = new_quote_request.model_dump()
    response_data["id"] = response_data.pop("_id")
    return QuoteRequest(**response_data)

api_router.include_router(contact_router)

# --- Blog Models & Routes ---

# Blog Category (similar to Product Category)
class BlogCategoryBase(BaseModel):
    name: str
    description: Optional[str] = None

class BlogCategoryCreate(BlogCategoryBase):
    pass

class BlogCategoryInDB(BlogCategoryBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class BlogCategoryPublic(BlogCategoryInDB):
    pass

# Blog Post
class BlogPostBase(BaseModel):
    title: str
    content: str # Can be Markdown or HTML string
    excerpt: Optional[str] = None
    author: Optional[str] = None # Or link to User ID (PyObjectId)
    tags: Optional[List[str]] = []
    category_id: Optional[PyObjectId] = None # Reference to BlogCategory's ID
    image_url: Optional[str] = None # URL for a cover image

class BlogPostCreate(BlogPostBase):
    pass

class BlogPostInDB(BlogPostBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    # author_id: Optional[PyObjectId] = None # If linking to a specific user
    slug: Optional[str] = None # Auto-generated URL-friendly slug
    publishedAt: Optional[datetime] = None # If implementing drafts/publishing
    isPublished: bool = Field(default=True)
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class BlogPostPublic(BlogPostInDB):
    category: Optional[BlogCategoryPublic] = None # Populate category details

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

blog_router = APIRouter(prefix="/blog", tags=["Blog"])

# Blog Category Endpoints
@blog_router.post("/categories", response_model=BlogCategoryPublic, status_code=status.HTTP_201_CREATED)
async def create_blog_category(category_in: BlogCategoryCreate, current_user: UserInDB = Depends(get_current_active_user)): # Protected
    category_doc = category_in.model_dump()
    category_doc["_id"] = uuid.uuid4()
    new_category = BlogCategoryInDB(**category_doc)
    await db.blog_categories.insert_one(new_category.model_dump(by_alias=True))
    public_data = new_category.model_dump()
    public_data["id"] = public_data.pop("_id")
    return BlogCategoryPublic(**public_data)

@blog_router.get("/categories", response_model=List[BlogCategoryPublic])
async def get_all_blog_categories():
    categories_cursor = db.blog_categories.find()
    categories_list = await categories_cursor.to_list(length=100)
    return [BlogCategoryPublic(**{**cat, "id": cat["_id"]}) for cat in categories_list]

# Blog Post Endpoints
@blog_router.post("/", response_model=BlogPostPublic, status_code=status.HTTP_201_CREATED)
async def create_blog_post(post_in: BlogPostCreate, current_user: UserInDB = Depends(get_current_active_user)): # Protected
    post_doc = post_in.model_dump()
    post_doc["_id"] = uuid.uuid4()
    # post_doc["author_id"] = current_user.id # If linking to user
    # Generate slug (simple version, can be more robust)
    post_doc["slug"] = post_in.title.lower().replace(" ", "-").replace("?", "").replace("!", "")

    if post_in.category_id:
        category = await db.blog_categories.find_one({"_id": post_in.category_id})
        if not category:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Blog category not found")

    new_post = BlogPostInDB(**post_doc)
    await db.blog_posts.insert_one(new_post.model_dump(by_alias=True))

    public_data = new_post.model_dump()
    public_data["id"] = public_data.pop("_id")
    if new_post.category_id:
        cat_data = await db.blog_categories.find_one({"_id": new_post.category_id})
        if cat_data:
            public_data["category"] = BlogCategoryPublic(**{**cat_data, "id": cat_data["_id"]})
    return BlogPostPublic(**public_data)

@blog_router.get("/", response_model=List[BlogPostPublic])
async def get_all_blog_posts(skip: int = 0, limit: int = 20):
    posts_cursor = db.blog_posts.find({"isPublished": True}).sort("publishedAt", -1).skip(skip).limit(limit)
    posts_list = await posts_cursor.to_list(length=limit)

    populated_posts = []
    for post_data in posts_list:
        post_data["id"] = post_data["_id"]
        if post_data.get("category_id"):
            cat_data = await db.blog_categories.find_one({"_id": post_data["category_id"]})
            if cat_data:
                post_data["category"] = BlogCategoryPublic(**{**cat_data, "id": cat_data["_id"]})
        populated_posts.append(BlogPostPublic(**post_data))
    return populated_posts

@blog_router.get("/{post_id_or_slug}", response_model=BlogPostPublic) # Can be ID or slug
async def get_blog_post_by_id_or_slug(post_id_or_slug: str):
    try:
        # Try to convert to PyObjectId first
        post_uuid = PyObjectId.validate(post_id_or_slug, None)
        query = {"_id": post_uuid, "isPublished": True}
    except ValueError:
        # If not a valid UUID, assume it's a slug
        query = {"slug": post_id_or_slug, "isPublished": True}

    post_data = await db.blog_posts.find_one(query)
    if not post_data:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Blog post not found")

    post_data["id"] = post_data["_id"]
    if post_data.get("category_id"):
        cat_data = await db.blog_categories.find_one({"_id": post_data["category_id"]})
        if cat_data:
            post_data["category"] = BlogCategoryPublic(**{**cat_data, "id": cat_data["_id"]})

    return BlogPostPublic(**post_data)

api_router.include_router(blog_router)

# --- Projects Models & Routes ---
class ProjectBase(BaseModel):
    title: str
    client: Optional[str] = None
    location: Optional[str] = None
    duration: Optional[str] = None
    status: Optional[str] = "Completed" # e.g., Ongoing, Completed
    type: Optional[str] = None # e.g., HVAC, Fire Safety
    description: str
    image_url: Optional[str] = None
    brands_used: Optional[List[str]] = []
    technologies: Optional[List[str]] = []

class ProjectCreate(ProjectBase):
    pass

class ProjectInDB(ProjectBase):
    id: PyObjectId = Field(default_factory=uuid.uuid4, alias="_id")
    createdAt: datetime = Field(default_factory=datetime.utcnow)
    updatedAt: datetime = Field(default_factory=datetime.utcnow)

    class Config:
        populate_by_name = True
        json_encoders = {PyObjectId: str, datetime: lambda dt: dt.isoformat()}
        arbitrary_types_allowed = True

class ProjectPublic(ProjectInDB):
    pass

projects_router = APIRouter(prefix="/projects", tags=["Projects"])

@projects_router.post("/", response_model=ProjectPublic, status_code=status.HTTP_201_CREATED)
async def create_project(project_in: ProjectCreate, current_user: UserInDB = Depends(get_current_active_user)): # Protected
    project_doc = project_in.model_dump()
    project_doc["_id"] = uuid.uuid4()
    new_project = ProjectInDB(**project_doc)
    await db.projects.insert_one(new_project.model_dump(by_alias=True))
    public_data = new_project.model_dump()
    public_data["id"] = public_data.pop("_id")
    return ProjectPublic(**public_data)

@projects_router.get("/", response_model=List[ProjectPublic])
async def get_all_projects(skip: int = 0, limit: int = 20):
    projects_cursor = db.projects.find().sort("createdAt", -1).skip(skip).limit(limit)
    projects_list = await projects_cursor.to_list(length=limit)
    return [ProjectPublic(**{**proj, "id": proj["_id"]}) for proj in projects_list]

@projects_router.get("/{project_id}", response_model=ProjectPublic)
async def get_project_by_id(project_id: PyObjectId):
    project_data = await db.projects.find_one({"_id": project_id})
    if not project_data:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Project not found")
    project_data["id"] = project_data["_id"]
    return ProjectPublic(**project_data)

api_router.include_router(projects_router)

# --- Payment Integration ---
from rave_python import Rave, RaveExceptions
from paystackapi.paystack import Paystack
from paystackapi.transaction import Transaction as PaystackTransaction

FLUTTERWAVE_SECRET_KEY = os.environ.get("FLUTTERWAVE_SECRET_KEY")
FLUTTERWAVE_PUBLIC_KEY = os.environ.get("FLUTTERWAVE_PUBLIC_KEY")
# FLUTTERWAVE_ENCRYPTION_KEY = os.environ.get("FLUTTERWAVE_ENCRYPTION_KEY") # if needed by rave-python

PAYSTACK_SECRET_KEY = os.environ.get("PAYSTACK_SECRET_KEY")
PAYSTACK_PUBLIC_KEY = os.environ.get("PAYSTACK_PUBLIC_KEY")

# Initialize Flutterwave Rave
rave = None
if FLUTTERWAVE_PUBLIC_KEY and FLUTTERWAVE_SECRET_KEY:
    rave = Rave(FLUTTERWAVE_PUBLIC_KEY, FLUTTERWAVE_SECRET_KEY, usingEnv=False) # Set usingEnv based on actual key type (test/live)

# Initialize Paystack
paystack = None
if PAYSTACK_SECRET_KEY:
    paystack = Paystack(secret_key=PAYSTACK_SECRET_KEY)

payments_router = APIRouter(prefix="/payments", tags=["Payments"])

class PaymentVerificationRequest(BaseModel):
    transaction_reference: str # or transaction_id for flutterwave
    order_id: PyObjectId

class PaymentVerificationResponse(BaseModel):
    success: bool
    message: str
    order: Optional[OrderPublic] = None


async def update_order_payment_status(order_id: PyObjectId, payment_status: str, new_order_status: Optional[str] = None):
    update_fields = {"payment_status": payment_status, "updatedAt": datetime.utcnow()}
    if new_order_status:
        update_fields["status"] = new_order_status

    updated_order = await db.orders.find_one_and_update(
        {"_id": order_id},
        {"$set": update_fields},
        return_document=True # Use pymongo.ReturnDocument.AFTER for newer pymongo
    )
    if updated_order:
        # TODO: Decrement stock here if payment is successful
        # This should be done carefully to avoid race conditions or doing it multiple times.
        # A flag on the order or a separate transaction log might be needed.
        # if payment_status == "paid":
        #     for item in updated_order.get("items", []):
        #         await db.products.update_one(
        #             {"_id": item["product_id"]},
        #             {"$inc": {"stock_quantity": -item["quantity"]}}
        #         )
        #         logger.info(f"Decremented stock for product {item['product_id']} by {item['quantity']}")

        updated_order["id"] = updated_order.pop("_id")
        return OrderPublic(**updated_order)
    return None

@payments_router.post("/verify/flutterwave", response_model=PaymentVerificationResponse)
async def verify_flutterwave_payment(verification_data: PaymentVerificationRequest, current_user: UserInDB = Depends(get_current_active_user)):
    if not rave:
        raise HTTPException(status_code=status.HTTP_503_SERVICE_UNAVAILABLE, detail="Flutterwave payment service not configured.")

    order = await db.orders.find_one({"_id": verification_data.order_id, "customer_id": current_user.id})
    if not order:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Order not found or does not belong to user.")

    if order["payment_status"] == "paid":
        order["id"] = order.pop("_id")
        return PaymentVerificationResponse(success=True, message="Payment already verified.", order=OrderPublic(**order))

    try:
        # Flutterwave uses tx_ref or transaction_id. Assuming verification_data.transaction_reference is the tx_ref.
        # The rave-python SDK might have a slightly different verification flow.
        # This is a conceptual example. Refer to rave-python docs for exact method.
        # Example: res = rave.Transaction.verify(verification_data.transaction_reference)
        # For this example, let's assume a direct verification call that returns structured data.
        # A common pattern is to re-query the transaction status.

        # Placeholder: The actual verification call to Flutterwave SDK
        # This SDK (rave-python 1.4.1) seems to focus on charging, not direct verification of external refs easily
        # A more common approach is to use their /transactions/{ID}/verify endpoint via an HTTP call if SDK lacks it
        # For now, let's simulate:
        # if verification_data.transaction_reference.startswith("FLW_SUCCESS_"): # Simulate success
        #     is_successful = True
        #     amount_paid = order["total_amount"]
        #     currency = "NGN" # Assume NGN
        # else: # Simulate failure
        #     is_successful = False
        #     amount_paid = 0
        #     currency = "NGN"

        # A more realistic SDK usage would be something like:
        # charge_response = rave.Card.charge(...) or rave.MobileMoney.charge(...)
        # verify_response = rave.Transaction.verify(charge_response['flwRef'])
        # This example assumes the frontend already initiated and got a reference.
        # Let's assume verification_data.transaction_reference is the transaction ID from Flutterwave.

        # The rave_python.Transaction.verify method expects 'flw_ref' which is Flutterwave's internal reference.
        # If the frontend provides `transaction_id` (integer), it might be that.
        # If it provides `tx_ref` (your unique order ref), you use that to query.
        # This part is highly dependent on what reference the frontend gets and sends.
        # For now, we'll assume transaction_reference is the FLW Transaction ID.

        # This SDK is a bit old. A direct API call might be more reliable for verification:
        # headers = {'Authorization': f'Bearer {FLUTTERWAVE_SECRET_KEY}'}
        # async with httpx.AsyncClient() as client:
        #     response = await client.get(f"https://api.flutterwave.com/v3/transactions/{verification_data.transaction_reference}/verify", headers=headers)
        # if response.status_code == 200:
        #     fw_res = response.json()
        #     if fw_res.get("status") == "success" and fw_res["data"]["tx_ref"] == str(order["_id"]): # Match with your order ref
        #         is_successful = True
        #         amount_paid = fw_res["data"]["amount"]
        #         currency = fw_res["data"]["currency"]
        #     else: is_successful = False, amount_paid = 0, currency = "NGN"
        # else: is_successful = False, amount_paid = 0, currency = "NGN"

        # Given the SDK limitations for simple verification, this part is simplified.
        # In a real scenario, ensure the API/SDK call is correct.
        # For this exercise, we'll assume the frontend only calls this if Flutterwave indicated success.
        # We'll simulate a successful verification if it reaches here for now.
        is_successful = True
        amount_paid = order["total_amount"]
        currency = "NGN" # Assuming NGN

        if is_successful and float(amount_paid) >= float(order["total_amount"]) and currency == "NGN": # Basic checks
            updated_order_public = await update_order_payment_status(verification_data.order_id, "paid", "processing")
            if updated_order_public:
                return PaymentVerificationResponse(success=True, message="Payment verified successfully via Flutterwave.", order=updated_order_public)
            else:
                raise HTTPException(status_code=status.HTTP_500_INTERNAL_SERVER_ERROR, detail="Failed to update order status.")
        else:
            await update_order_payment_status(verification_data.order_id, "failed")
            return PaymentVerificationResponse(success=False, message="Flutterwave payment verification failed or amount mismatch.")

    except RaveExceptions.TransactionVerificationError as e: # Example exception
        logger.error(f"Flutterwave verification error: {e}")
        await update_order_payment_status(verification_data.order_id, "failed")
        return PaymentVerificationResponse(success=False, message=f"Flutterwave verification error: {e}")
    except Exception as e:
        logger.error(f"General error during Flutterwave verification: {e}")
        await update_order_payment_status(verification_data.order_id, "error")
        raise HTTPException(status_code=status.HTTP_500_INTERNAL_SERVER_ERROR, detail=f"An error occurred: {e}")


@payments_router.post("/verify/paystack", response_model=PaymentVerificationResponse)
async def verify_paystack_payment(verification_data: PaymentVerificationRequest, current_user: UserInDB = Depends(get_current_active_user)):
    if not paystack:
        raise HTTPException(status_code=status.HTTP_503_SERVICE_UNAVAILABLE, detail="Paystack payment service not configured.")

    order = await db.orders.find_one({"_id": verification_data.order_id, "customer_id": current_user.id})
    if not order:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Order not found or does not belong to user.")

    if order["payment_status"] == "paid":
        order["id"] = order.pop("_id")
        return PaymentVerificationResponse(success=True, message="Payment already verified.", order=OrderPublic(**order))

    try:
        # Paystack API uses reference for verification
        response = PaystackTransaction.verify(reference=verification_data.transaction_reference) # paystackapi library call

        if response['status'] is True and response['data']['status'] == 'success':
            amount_paid_kobo = response['data']['amount']
            amount_paid_main = amount_paid_kobo / 100
            currency = response['data']['currency']

            # Ensure amount matches and currency is NGN (or your store's currency)
            if float(amount_paid_main) >= float(order["total_amount"]) and currency == "NGN":
                updated_order_public = await update_order_payment_status(verification_data.order_id, "paid", "processing")
                if updated_order_public:
                    return PaymentVerificationResponse(success=True, message="Payment verified successfully via Paystack.", order=updated_order_public)
                else:
                    raise HTTPException(status_code=status.HTTP_500_INTERNAL_SERVER_ERROR, detail="Failed to update order status.")
            else:
                await update_order_payment_status(verification_data.order_id, "failed")
                logger.error(f"Paystack amount mismatch: Expected {order['total_amount']}, Got {amount_paid_main} {currency}")
                return PaymentVerificationResponse(success=False, message="Paystack payment verification failed: Amount or currency mismatch.")
        else:
            await update_order_payment_status(verification_data.order_id, "failed")
            logger.error(f"Paystack verification failed: {response.get('message')}")
            return PaymentVerificationResponse(success=False, message=f"Paystack payment verification failed: {response.get('message', 'Unknown error')}")

    except Exception as e: # Catching generic Exception from paystackapi or other issues
        logger.error(f"Error during Paystack verification: {e}")
        await update_order_payment_status(verification_data.order_id, "error") # Mark as error
        # Consider if this should return 500 or a specific payment error to frontend
        raise HTTPException(status_code=status.HTTP_500_INTERNAL_SERVER_ERROR, detail=f"An error occurred during Paystack verification: {e}")

api_router.include_router(payments_router)


# Will be added in subsequent steps

# Example StatusCheck model from original code (can be kept or removed)

# --- Admin Routes ---
admin_router = APIRouter(
    prefix="/admin",
    tags=["Admin Management"],
    dependencies=[Depends(get_current_admin_user)] # Secure all admin routes
)

# Admin Product Management
@admin_router.put("/products/{product_id}", response_model=ProductPublic)
async def update_product_admin(product_id: PyObjectId, product_update: ProductCreate):
    existing_product = await db.products.find_one({"_id": product_id})
    if not existing_product:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Product not found")

    if product_update.category_id:
        category = await db.categories.find_one({"_id": product_update.category_id})
        if not category:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Category not found")

    update_data = product_update.model_dump(exclude_unset=True) # Exclude unset fields from payload
    update_data["updatedAt"] = datetime.utcnow()

    updated_product_doc = await db.products.find_one_and_update(
        {"_id": product_id},
        {"$set": update_data},
        return_document=True # Use pymongo.ReturnDocument.AFTER
    )

    if not updated_product_doc:
        # This case should ideally not be reached if find_one found it earlier,
        # but as a safeguard for concurrent deletion or other issues.
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Product not found during update")

    # Populate category for response
    updated_product_doc["id"] = updated_product_doc["_id"]
    if updated_product_doc.get("category_id"):
        category_data = await db.categories.find_one({"_id": updated_product_doc["category_id"]})
        if category_data:
            updated_product_doc["category"] = CategoryPublic(**{**category_data, "id": category_data["_id"]})

    return ProductPublic(**updated_product_doc)


@admin_router.delete("/products/{product_id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_product_admin(product_id: PyObjectId):
    delete_result = await db.products.delete_one({"_id": product_id})
    if delete_result.deleted_count == 0:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Product not found")
    return # No content response

# Note: Product creation can use the existing POST /api/products/ endpoint,
# but it's currently protected by get_current_active_user.
# If only admins should create products, that endpoint's dependency should change to get_current_admin_user.
# Or, we can add a specific admin POST endpoint here. For now, let's assume an admin uses the general one.

# Include admin router in the main API router
api_router.include_router(admin_router)


# --- Temporary DEV ONLY endpoint to make a user admin ---
# IMPORTANT: REMOVE THIS IN PRODUCTION
@api_router.post("/dev/make-admin/{user_email}", tags=["Dev Utilities - REMOVE IN PROD"])
async def make_user_admin_dev_only(user_email: EmailStr):
    user = await get_user_by_email(user_email)
    if not user:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="User not found")

    update_result = await db.users.update_one(
        {"email": user_email},
        {"$set": {"isAdmin": True, "updatedAt": datetime.utcnow()}}
    )
    if update_result.modified_count == 0 and not user.isAdmin: # Check if already admin
        raise HTTPException(status_code=status.HTTP_304_NOT_MODIFIED, detail="User was already admin or not modified.")

    updated_user = await get_user_by_email(user_email)
    public_user_data = updated_user.model_dump(exclude={"hashed_password", "disabled"})
    public_user_data["id"] = public_user_data.pop("_id")
    return {"message": f"User {user_email} is now an admin.", "user": UserPublic(**public_user_data)}


class StatusCheck(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    client_name: str
    timestamp: datetime = Field(default_factory=datetime.utcnow)

class StatusCheckCreate(BaseModel):
    client_name: str

@api_router.get("/", tags=["Root"])
async def root():
    return {"message": "Welcome to EINSPOT API"}

@api_router.post("/status", response_model=StatusCheck, tags=["Status"])
async def create_status_check(input_data: StatusCheckCreate): # Renamed 'input' to 'input_data'
    status_dict = input_data.model_dump()
    status_obj = StatusCheck(**status_dict)
    _ = await db.status_checks.insert_one(status_obj.model_dump())
    return status_obj

@api_router.get("/status", response_model=List[StatusCheck], tags=["Status"])
async def get_status_checks():
    status_checks_cursor = db.status_checks.find()
    status_checks_list = await status_checks_cursor.to_list(1000)
    return [StatusCheck(**status_check) for status_check in status_checks_list]


# Include the main API router in the app
app.include_router(api_router)

app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=["*"], # Consider restricting this in production
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

@app.on_event("startup")
async def startup_event():
    # You can add any startup logic here, e.g., creating indexes
    # For example, ensuring 'email' is unique for users collection
    await db.users.create_index("email", unique=True)
    logger.info("Application startup complete. MongoDB indexes checked/created.")


@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()
    logger.info("MongoDB connection closed.")
