from fastapi import Request, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
from fastapi.middleware.trustedhost import TrustedHostMiddleware
from starlette.middleware.sessions import SessionMiddleware
import time
import hashlib
from collections import defaultdict
from typing import Dict, List
import logging

logger = logging.getLogger(__name__)

class SecurityMiddleware:
    def __init__(self):
        self.rate_limits: Dict[str, List[float]] = defaultdict(list)
        self.blocked_ips: set = set()
        
    def add_security_middleware(self, app, config):
        """Add all security middleware to the FastAPI app"""
        
        # CORS middleware
        app.add_middleware(
            CORSMiddleware,
            allow_origins=config.CORS_ORIGINS,
            allow_credentials=True,
            allow_methods=["GET", "POST", "PUT", "DELETE", "OPTIONS"],
            allow_headers=["*"],
        )
        
        # Trusted host middleware
        app.add_middleware(
            TrustedHostMiddleware,
            allowed_hosts=["einspot.com.ng", "www.einspot.com.ng", "localhost"]
        )
        
        # Session middleware
        app.add_middleware(
            SessionMiddleware,
            secret_key=config.JWT_SECRET,
            max_age=86400,  # 24 hours
            same_site="lax",
            https_only=True
        )
        
        # Custom security middleware
        @app.middleware("http")
        async def security_middleware(request: Request, call_next):
            # Rate limiting
            client_ip = self.get_client_ip(request)
            
            if client_ip in self.blocked_ips:
                raise HTTPException(
                    status_code=status.HTTP_429_TOO_MANY_REQUESTS,
                    detail="IP address blocked due to excessive requests"
                )
            
            if not self.check_rate_limit(client_ip, config.RATE_LIMIT_PER_MINUTE):
                logger.warning(f"Rate limit exceeded for IP: {client_ip}")
                raise HTTPException(
                    status_code=status.HTTP_429_TOO_MANY_REQUESTS,
                    detail="Rate limit exceeded"
                )
            
            # Security headers
            response = await call_next(request)
            
            response.headers["X-Content-Type-Options"] = "nosniff"
            response.headers["X-Frame-Options"] = "DENY"
            response.headers["X-XSS-Protection"] = "1; mode=block"
            response.headers["Strict-Transport-Security"] = "max-age=31536000; includeSubDomains"
            response.headers["Referrer-Policy"] = "strict-origin-when-cross-origin"
            response.headers["Permissions-Policy"] = "geolocation=(), microphone=(), camera=()"
            
            return response
    
    def get_client_ip(self, request: Request) -> str:
        """Get the real client IP address"""
        forwarded_for = request.headers.get("X-Forwarded-For")
        if forwarded_for:
            return forwarded_for.split(",")[0].strip()
        
        real_ip = request.headers.get("X-Real-IP")
        if real_ip:
            return real_ip
        
        return request.client.host if request.client else "unknown"
    
    def check_rate_limit(self, client_ip: str, limit_per_minute: int) -> bool:
        """Check if client IP is within rate limits"""
        current_time = time.time()
        minute_ago = current_time - 60
        
        # Clean old entries
        self.rate_limits[client_ip] = [
            timestamp for timestamp in self.rate_limits[client_ip]
            if timestamp > minute_ago
        ]
        
        # Check current rate
        if len(self.rate_limits[client_ip]) >= limit_per_minute:
            # Block IP if consistently exceeding limits
            if len(self.rate_limits[client_ip]) > limit_per_minute * 2:
                self.blocked_ips.add(client_ip)
                logger.error(f"IP {client_ip} blocked for excessive requests")
            return False
        
        # Add current request
        self.rate_limits[client_ip].append(current_time)
        return True

class InputValidation:
    """Input validation and sanitization"""
    
    @staticmethod
    def sanitize_string(value: str, max_length: int = 1000) -> str:
        """Sanitize string input"""
        if not isinstance(value, str):
            raise ValueError("Input must be a string")
        
        # Remove null bytes and control characters
        sanitized = ''.join(char for char in value if ord(char) >= 32 or char in '\n\r\t')
        
        # Limit length
        if len(sanitized) > max_length:
            raise ValueError(f"Input too long. Maximum {max_length} characters allowed")
        
        return sanitized.strip()
    
    @staticmethod
    def validate_email(email: str) -> str:
        """Validate and sanitize email"""
        import re
        
        email = InputValidation.sanitize_string(email, 254)
        
        email_pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        if not re.match(email_pattern, email):
            raise ValueError("Invalid email format")
        
        return email.lower()
    
    @staticmethod
    def validate_phone(phone: str) -> str:
        """Validate and sanitize phone number"""
        import re
        
        phone = InputValidation.sanitize_string(phone, 20)
        
        # Remove all non-digit characters except +
        phone = re.sub(r'[^\d+]', '', phone)
        
        # Basic phone validation (adjust pattern as needed)
        phone_pattern = r'^\+?[1-9]\d{1,14}$'
        if not re.match(phone_pattern, phone):
            raise ValueError("Invalid phone number format")
        
        return phone

class PasswordSecurity:
    """Password hashing and validation"""
    
    @staticmethod
    def hash_password(password: str) -> str:
        """Hash password using bcrypt"""
        from passlib.context import CryptContext
        
        pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
        return pwd_context.hash(password)
    
    @staticmethod
    def verify_password(plain_password: str, hashed_password: str) -> bool:
        """Verify password against hash"""
        from passlib.context import CryptContext
        
        pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
        return pwd_context.verify(plain_password, hashed_password)
    
    @staticmethod
    def validate_password_strength(password: str) -> bool:
        """Validate password strength"""
        import re
        
        if len(password) < 8:
            raise ValueError("Password must be at least 8 characters long")
        
        if not re.search(r'[A-Z]', password):
            raise ValueError("Password must contain at least one uppercase letter")
        
        if not re.search(r'[a-z]', password):
            raise ValueError("Password must contain at least one lowercase letter")
        
        if not re.search(r'\d', password):
            raise ValueError("Password must contain at least one digit")
        
        if not re.search(r'[!@#$%^&*(),.?":{}|<>]', password):
            raise ValueError("Password must contain at least one special character")
        
        return True

# Create global security instance
security = SecurityMiddleware()