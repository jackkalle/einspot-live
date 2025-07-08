import logging
import sys
from datetime import datetime
from pathlib import Path
import json
from typing import Any, Dict

class ProductionLogger:
    def __init__(self, config):
        self.config = config
        self.setup_logging()
    
    def setup_logging(self):
        """Setup production logging configuration"""
        
        # Create logs directory
        log_dir = Path("/app/logs")
        log_dir.mkdir(exist_ok=True)
        
        # Configure root logger
        logging.basicConfig(
            level=getattr(logging, self.config.LOG_LEVEL.upper()),
            format=self.config.LOG_FORMAT,
            handlers=[
                logging.StreamHandler(sys.stdout),
                logging.FileHandler(log_dir / "app.log"),
                logging.FileHandler(log_dir / "error.log", level=logging.ERROR)
            ]
        )
        
        # Configure specific loggers
        self.setup_access_logger(log_dir)
        self.setup_security_logger(log_dir)
        self.setup_business_logger(log_dir)
    
    def setup_access_logger(self, log_dir: Path):
        """Setup access logging"""
        access_logger = logging.getLogger("access")
        access_handler = logging.FileHandler(log_dir / "access.log")
        access_handler.setFormatter(
            logging.Formatter('%(asctime)s - %(message)s')
        )
        access_logger.addHandler(access_handler)
        access_logger.setLevel(logging.INFO)
    
    def setup_security_logger(self, log_dir: Path):
        """Setup security event logging"""
        security_logger = logging.getLogger("security")
        security_handler = logging.FileHandler(log_dir / "security.log")
        security_handler.setFormatter(
            logging.Formatter('%(asctime)s - SECURITY - %(levelname)s - %(message)s')
        )
        security_logger.addHandler(security_handler)
        security_logger.setLevel(logging.WARNING)
    
    def setup_business_logger(self, log_dir: Path):
        """Setup business event logging"""
        business_logger = logging.getLogger("business")
        business_handler = logging.FileHandler(log_dir / "business.log")
        business_handler.setFormatter(
            logging.Formatter('%(asctime)s - BUSINESS - %(message)s')
        )
        business_logger.addHandler(business_handler)
        business_logger.setLevel(logging.INFO)

class StructuredLogger:
    """Structured logging for better log analysis"""
    
    def __init__(self, name: str):
        self.logger = logging.getLogger(name)
    
    def log_event(self, event_type: str, data: Dict[str, Any], level: str = "INFO"):
        """Log structured event"""
        log_entry = {
            "timestamp": datetime.utcnow().isoformat(),
            "event_type": event_type,
            "data": data
        }
        
        log_level = getattr(logging, level.upper())
        self.logger.log(log_level, json.dumps(log_entry))
    
    def log_api_request(self, method: str, path: str, user_id: str = None, 
                       ip_address: str = None, response_time: float = None):
        """Log API request"""
        self.log_event("api_request", {
            "method": method,
            "path": path,
            "user_id": user_id,
            "ip_address": ip_address,
            "response_time_ms": response_time
        })
    
    def log_authentication(self, event: str, user_id: str = None, 
                          ip_address: str = None, success: bool = True):
        """Log authentication events"""
        self.log_event("authentication", {
            "event": event,
            "user_id": user_id,
            "ip_address": ip_address,
            "success": success
        }, level="WARNING" if not success else "INFO")
    
    def log_business_event(self, event: str, data: Dict[str, Any]):
        """Log business events"""
        business_logger = logging.getLogger("business")
        log_entry = {
            "timestamp": datetime.utcnow().isoformat(),
            "event": event,
            "data": data
        }
        business_logger.info(json.dumps(log_entry))
    
    def log_error(self, error: Exception, context: Dict[str, Any] = None):
        """Log errors with context"""
        self.log_event("error", {
            "error_type": type(error).__name__,
            "error_message": str(error),
            "context": context or {}
        }, level="ERROR")

# Create logger instances
app_logger = StructuredLogger("app")
security_logger = StructuredLogger("security")
business_logger = StructuredLogger("business")