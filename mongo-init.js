// MongoDB initialization script for production

// Switch to the application database
db = db.getSiblingDB('einspot_db');

// Create application user
db.createUser({
  user: 'einspot_user',
  pwd: process.env.MONGO_USER_PASSWORD || 'change_this_password',
  roles: [
    {
      role: 'readWrite',
      db: 'einspot_db'
    }
  ]
});

// Create collections with validation
db.createCollection('users', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['email', 'password', 'firstName', 'lastName', 'createdAt'],
      properties: {
        email: {
          bsonType: 'string',
          pattern: '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        },
        password: {
          bsonType: 'string',
          minLength: 8
        },
        firstName: {
          bsonType: 'string',
          minLength: 1,
          maxLength: 50
        },
        lastName: {
          bsonType: 'string',
          minLength: 1,
          maxLength: 50
        },
        phone: {
          bsonType: ['string', 'null']
        },
        company: {
          bsonType: ['string', 'null']
        },
        isAdmin: {
          bsonType: 'bool'
        },
        isActive: {
          bsonType: 'bool'
        },
        createdAt: {
          bsonType: 'date'
        },
        updatedAt: {
          bsonType: 'date'
        }
      }
    }
  }
});

db.createCollection('products', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['name', 'price', 'category', 'createdAt'],
      properties: {
        name: {
          bsonType: 'string',
          minLength: 1,
          maxLength: 200
        },
        description: {
          bsonType: ['string', 'null']
        },
        price: {
          bsonType: 'number',
          minimum: 0
        },
        category: {
          bsonType: 'string'
        },
        brand: {
          bsonType: ['string', 'null']
        },
        stockQuantity: {
          bsonType: 'number',
          minimum: 0
        },
        images: {
          bsonType: 'array'
        },
        specifications: {
          bsonType: ['object', 'null']
        },
        isActive: {
          bsonType: 'bool'
        },
        createdAt: {
          bsonType: 'date'
        },
        updatedAt: {
          bsonType: 'date'
        }
      }
    }
  }
});

db.createCollection('orders', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['userId', 'items', 'totalAmount', 'status', 'createdAt'],
      properties: {
        userId: {
          bsonType: 'objectId'
        },
        items: {
          bsonType: 'array',
          minItems: 1
        },
        totalAmount: {
          bsonType: 'number',
          minimum: 0
        },
        status: {
          bsonType: 'string',
          enum: ['pending', 'processing', 'shipped', 'delivered', 'cancelled']
        },
        shippingAddress: {
          bsonType: 'object'
        },
        paymentMethod: {
          bsonType: 'string'
        },
        paymentStatus: {
          bsonType: 'string',
          enum: ['pending', 'paid', 'failed', 'refunded']
        },
        createdAt: {
          bsonType: 'date'
        },
        updatedAt: {
          bsonType: 'date'
        }
      }
    }
  }
});

db.createCollection('contacts', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['name', 'email', 'message', 'createdAt'],
      properties: {
        name: {
          bsonType: 'string',
          minLength: 1,
          maxLength: 100
        },
        email: {
          bsonType: 'string',
          pattern: '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        },
        phone: {
          bsonType: ['string', 'null']
        },
        company: {
          bsonType: ['string', 'null']
        },
        service: {
          bsonType: ['string', 'null']
        },
        message: {
          bsonType: 'string',
          minLength: 10,
          maxLength: 2000
        },
        status: {
          bsonType: 'string',
          enum: ['new', 'in_progress', 'resolved', 'closed']
        },
        createdAt: {
          bsonType: 'date'
        }
      }
    }
  }
});

// Create indexes for better performance
db.users.createIndex({ email: 1 }, { unique: true });
db.users.createIndex({ createdAt: -1 });
db.users.createIndex({ isActive: 1 });

db.products.createIndex({ name: 'text', description: 'text' });
db.products.createIndex({ category: 1 });
db.products.createIndex({ brand: 1 });
db.products.createIndex({ price: 1 });
db.products.createIndex({ isActive: 1 });
db.products.createIndex({ createdAt: -1 });

db.orders.createIndex({ userId: 1 });
db.orders.createIndex({ status: 1 });
db.orders.createIndex({ createdAt: -1 });
db.orders.createIndex({ paymentStatus: 1 });

db.contacts.createIndex({ email: 1 });
db.contacts.createIndex({ status: 1 });
db.contacts.createIndex({ createdAt: -1 });

// Insert sample admin user (change password in production)
db.users.insertOne({
  email: 'admin@einspot.com.ng',
  password: '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj/RK.PZvO.G', // 'admin123' - CHANGE THIS
  firstName: 'Admin',
  lastName: 'User',
  phone: '+234 812 364 7982',
  company: 'EINSPOT SOLUTIONS NIG LTD',
  isAdmin: true,
  isActive: true,
  createdAt: new Date(),
  updatedAt: new Date()
});

// Insert sample product categories
db.categories.insertMany([
  {
    name: 'HVAC Systems',
    description: 'Heating, Ventilation, and Air Conditioning systems',
    isActive: true,
    createdAt: new Date()
  },
  {
    name: 'Water Heaters',
    description: 'Rheem and other water heating solutions',
    isActive: true,
    createdAt: new Date()
  },
  {
    name: 'Fire Safety',
    description: 'Fire suppression and safety systems',
    isActive: true,
    createdAt: new Date()
  },
  {
    name: 'Building Automation',
    description: 'Smart building management systems',
    isActive: true,
    createdAt: new Date()
  },
  {
    name: 'Electrical Systems',
    description: 'Electrical engineering solutions',
    isActive: true,
    createdAt: new Date()
  },
  {
    name: 'Plumbing Systems',
    description: 'Plumbing and water management systems',
    isActive: true,
    createdAt: new Date()
  }
]);

print('Database initialization completed successfully');