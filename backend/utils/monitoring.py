import time
import psutil
import logging
from datetime import datetime, timedelta
from typing import Dict, Any
from dataclasses import dataclass
import asyncio

@dataclass
class HealthStatus:
    status: str
    timestamp: datetime
    details: Dict[str, Any]

class HealthChecker:
    """Application health monitoring"""
    
    def __init__(self, config):
        self.config = config
        self.logger = logging.getLogger("monitoring")
    
    async def check_health(self) -> HealthStatus:
        """Comprehensive health check"""
        details = {}
        status = "healthy"
        
        try:
            # Check database connectivity
            db_status = await self.check_database()
            details["database"] = db_status
            if not db_status["healthy"]:
                status = "unhealthy"
            
            # Check system resources
            system_status = self.check_system_resources()
            details["system"] = system_status
            if not system_status["healthy"]:
                status = "degraded" if status == "healthy" else "unhealthy"
            
            # Check external services
            external_status = await self.check_external_services()
            details["external_services"] = external_status
            
            # Check application metrics
            app_metrics = self.get_application_metrics()
            details["application"] = app_metrics
            
        except Exception as e:
            self.logger.error(f"Health check failed: {e}")
            status = "unhealthy"
            details["error"] = str(e)
        
        return HealthStatus(
            status=status,
            timestamp=datetime.utcnow(),
            details=details
        )
    
    async def check_database(self) -> Dict[str, Any]:
        """Check database connectivity and performance"""
        try:
            from motor.motor_asyncio import AsyncIOMotorClient
            
            start_time = time.time()
            client = AsyncIOMotorClient(self.config.MONGODB_URI)
            
            # Simple ping test
            await client.admin.command('ping')
            
            response_time = (time.time() - start_time) * 1000
            
            # Check database stats
            db = client.get_default_database()
            stats = await db.command("dbStats")
            
            client.close()
            
            return {
                "healthy": True,
                "response_time_ms": round(response_time, 2),
                "collections": stats.get("collections", 0),
                "data_size_mb": round(stats.get("dataSize", 0) / 1024 / 1024, 2)
            }
            
        except Exception as e:
            self.logger.error(f"Database health check failed: {e}")
            return {
                "healthy": False,
                "error": str(e)
            }
    
    def check_system_resources(self) -> Dict[str, Any]:
        """Check system resource usage"""
        try:
            cpu_percent = psutil.cpu_percent(interval=1)
            memory = psutil.virtual_memory()
            disk = psutil.disk_usage('/')
            
            # Define thresholds
            cpu_threshold = 80
            memory_threshold = 85
            disk_threshold = 90
            
            healthy = (
                cpu_percent < cpu_threshold and
                memory.percent < memory_threshold and
                disk.percent < disk_threshold
            )
            
            return {
                "healthy": healthy,
                "cpu_percent": cpu_percent,
                "memory_percent": memory.percent,
                "disk_percent": disk.percent,
                "memory_available_gb": round(memory.available / 1024 / 1024 / 1024, 2),
                "disk_free_gb": round(disk.free / 1024 / 1024 / 1024, 2)
            }
            
        except Exception as e:
            self.logger.error(f"System resource check failed: {e}")
            return {
                "healthy": False,
                "error": str(e)
            }
    
    async def check_external_services(self) -> Dict[str, Any]:
        """Check external service connectivity"""
        import aiohttp
        
        services = {
            "paystack": "https://api.paystack.co",
            "flutterwave": "https://api.flutterwave.com",
        }
        
        results = {}
        
        async with aiohttp.ClientSession(timeout=aiohttp.ClientTimeout(total=5)) as session:
            for service, url in services.items():
                try:
                    start_time = time.time()
                    async with session.get(url) as response:
                        response_time = (time.time() - start_time) * 1000
                        results[service] = {
                            "healthy": response.status < 500,
                            "status_code": response.status,
                            "response_time_ms": round(response_time, 2)
                        }
                except Exception as e:
                    results[service] = {
                        "healthy": False,
                        "error": str(e)
                    }
        
        return results
    
    def get_application_metrics(self) -> Dict[str, Any]:
        """Get application-specific metrics"""
        try:
            process = psutil.Process()
            
            return {
                "uptime_seconds": time.time() - process.create_time(),
                "memory_usage_mb": round(process.memory_info().rss / 1024 / 1024, 2),
                "cpu_percent": process.cpu_percent(),
                "threads": process.num_threads(),
                "open_files": len(process.open_files())
            }
            
        except Exception as e:
            self.logger.error(f"Application metrics check failed: {e}")
            return {"error": str(e)}

class PerformanceMonitor:
    """Performance monitoring and metrics collection"""
    
    def __init__(self):
        self.metrics = {}
        self.logger = logging.getLogger("performance")
    
    def record_request_time(self, endpoint: str, method: str, duration: float):
        """Record API request timing"""
        key = f"{method}:{endpoint}"
        
        if key not in self.metrics:
            self.metrics[key] = {
                "count": 0,
                "total_time": 0,
                "min_time": float('inf'),
                "max_time": 0
            }
        
        metric = self.metrics[key]
        metric["count"] += 1
        metric["total_time"] += duration
        metric["min_time"] = min(metric["min_time"], duration)
        metric["max_time"] = max(metric["max_time"], duration)
        
        # Log slow requests
        if duration > 1000:  # 1 second
            self.logger.warning(f"Slow request: {key} took {duration:.2f}ms")
    
    def get_metrics_summary(self) -> Dict[str, Any]:
        """Get performance metrics summary"""
        summary = {}
        
        for endpoint, metric in self.metrics.items():
            if metric["count"] > 0:
                summary[endpoint] = {
                    "count": metric["count"],
                    "avg_time_ms": round(metric["total_time"] / metric["count"], 2),
                    "min_time_ms": round(metric["min_time"], 2),
                    "max_time_ms": round(metric["max_time"], 2)
                }
        
        return summary
    
    def reset_metrics(self):
        """Reset metrics (useful for periodic reporting)"""
        self.metrics.clear()

# Global instances
health_checker = None
performance_monitor = PerformanceMonitor()

def initialize_monitoring(config):
    """Initialize monitoring components"""
    global health_checker
    health_checker = HealthChecker(config)