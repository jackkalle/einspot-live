# EINSPOT SOLUTIONS - Production Deployment Guide

This guide covers the complete production deployment process for the EINSPOT SOLUTIONS web application.

## Prerequisites

### System Requirements
- Ubuntu 20.04 LTS or newer
- Docker 20.10+ and Docker Compose 2.0+
- Minimum 4GB RAM, 2 CPU cores
- 50GB+ available disk space
- Domain name pointing to your server

### Required Services
- MongoDB (included in Docker setup)
- SSL Certificate (Let's Encrypt - automated)
- Email SMTP service (Gmail, SendGrid, etc.)
- Payment gateways (Paystack, Flutterwave)
- Google Analytics (optional)

## Quick Deployment

### 1. Clone and Setup
```bash
git clone <repository-url> einspot-app
cd einspot-app
chmod +x deploy.sh backup.sh
```

### 2. Run Deployment Script
```bash
./deploy.sh
```

The script will:
- Check system requirements
- Setup SSL certificates
- Create environment configuration
- Deploy the application
- Setup monitoring and backups

### 3. Configure Services
Edit `/opt/einspot-app/.env` and configure:
- Email SMTP settings
- Payment gateway credentials
- Google Analytics ID
- Other service credentials

### 4. Restart Services
```bash
cd /opt/einspot-app
docker-compose -f docker-compose.prod.yml restart
```

## Manual Deployment

### 1. System Preparation
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Logout and login to apply Docker group changes
```

### 2. SSL Certificate Setup
```bash
# Install Certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d einspot.com.ng -d www.einspot.com.ng

# Copy certificates
sudo mkdir -p /opt/ssl
sudo cp /etc/letsencrypt/live/einspot.com.ng/fullchain.pem /opt/ssl/
sudo cp /etc/letsencrypt/live/einspot.com.ng/privkey.pem /opt/ssl/
```

### 3. Environment Configuration
```bash
# Create deployment directory
sudo mkdir -p /opt/einspot-app
sudo chown -R $USER:$USER /opt/einspot-app

# Copy application files
cp -r . /opt/einspot-app/
cd /opt/einspot-app

# Create environment file
cp .env.production .env
# Edit .env with your configuration
```

### 4. Deploy Application
```bash
# Build and start services
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d

# Check status
docker-compose -f docker-compose.prod.yml ps
```

## Configuration

### Environment Variables

#### Required Variables
```bash
# Domain
DOMAIN=einspot.com.ng
FRONTEND_URL=https://einspot.com.ng
BACKEND_URL=https://einspot.com.ng/api

# Database
MONGODB_URI=mongodb://einspot_user:password@mongodb:27017/einspot_db
MONGO_ROOT_USERNAME=root
MONGO_ROOT_PASSWORD=secure_password

# Security
JWT_SECRET=your_very_long_jwt_secret_minimum_32_characters
ENCRYPTION_KEY=your_32_character_encryption_key

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password
FROM_EMAIL=noreply@einspot.com.ng
```

#### Optional Variables
```bash
# Analytics
GA_MEASUREMENT_ID=G-XXXXXXXXXX

# Payment Gateways
PAYSTACK_SECRET_KEY=sk_live_...
PAYSTACK_PUBLIC_KEY=pk_live_...
FLUTTERWAVE_SECRET_KEY=FLWSECK_LIVE-...
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_LIVE-...

# Monitoring
SENTRY_DSN=https://...@sentry.io/...
LOG_LEVEL=info

# Backup
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_S3_BUCKET=einspot-backups
```

### SSL Certificate Renewal
```bash
# Add to crontab for auto-renewal
0 12 * * * /usr/bin/certbot renew --quiet --post-hook 'docker-compose -f /opt/einspot-app/docker-compose.prod.yml restart nginx'
```

## Monitoring and Maintenance

### Health Checks
- Application: `https://einspot.com.ng/health`
- API: `https://einspot.com.ng/api/health`
- Database: Monitored internally

### Log Management
```bash
# View application logs
docker-compose -f docker-compose.prod.yml logs -f backend
docker-compose -f docker-compose.prod.yml logs -f frontend

# Log files location
/var/log/einspot/
```

### Backup System
```bash
# Manual backup
./backup.sh

# Automated backups (configured in deploy.sh)
# Daily at 2 AM via cron
0 2 * * * /opt/einspot-app/backup.sh
```

### Database Management
```bash
# Access MongoDB
docker exec -it mongodb mongo -u root -p

# Database backup
docker exec mongodb mongodump --out /tmp/backup
docker cp mongodb:/tmp/backup ./mongodb-backup

# Database restore
docker cp ./mongodb-backup mongodb:/tmp/restore
docker exec mongodb mongorestore /tmp/restore
```

## Security Features

### Implemented Security Measures
- SSL/TLS encryption (HTTPS)
- Rate limiting
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Security headers
- Password hashing (bcrypt)
- JWT authentication
- IP blocking for abuse

### Security Headers
- Strict-Transport-Security
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- Referrer-Policy
- Content-Security-Policy

## Performance Optimization

### Frontend Optimizations
- Code splitting
- Image optimization
- Gzip compression
- Browser caching
- CDN-ready assets
- Minification

### Backend Optimizations
- Database indexing
- Connection pooling
- Response caching
- API rate limiting
- Async processing

### Infrastructure
- Nginx reverse proxy
- Load balancing ready
- Docker multi-stage builds
- Resource limits

## Troubleshooting

### Common Issues

#### Application Won't Start
```bash
# Check logs
docker-compose -f docker-compose.prod.yml logs

# Check system resources
docker stats
df -h
free -h
```

#### SSL Certificate Issues
```bash
# Check certificate validity
openssl x509 -in /opt/ssl/fullchain.pem -text -noout

# Renew certificate
sudo certbot renew --force-renewal
```

#### Database Connection Issues
```bash
# Check MongoDB status
docker exec mongodb mongo --eval "db.adminCommand('ping')"

# Reset database password
docker exec -it mongodb mongo -u root -p
```

#### Performance Issues
```bash
# Monitor resources
htop
iotop
docker stats

# Check application metrics
curl https://einspot.com.ng/api/metrics
```

### Emergency Procedures

#### Rollback Deployment
```bash
# Stop current deployment
docker-compose -f docker-compose.prod.yml down

# Restore from backup
cd /opt/einspot-backups
tar -xzf einspot-backup-YYYYMMDD-HHMMSS.tar.gz
# Follow restore procedures
```

#### Database Recovery
```bash
# Restore from backup
docker cp mongodb-backup mongodb:/tmp/restore
docker exec mongodb mongorestore --drop /tmp/restore
```

## Scaling and High Availability

### Horizontal Scaling
- Load balancer configuration
- Multiple backend instances
- Database replication
- Session management

### Monitoring and Alerting
- Health check endpoints
- Log aggregation
- Performance metrics
- Error tracking
- Uptime monitoring

## Support and Maintenance

### Regular Maintenance Tasks
- Security updates
- Certificate renewal
- Database optimization
- Log rotation
- Backup verification
- Performance monitoring

### Update Procedures
1. Create backup
2. Test in staging
3. Deploy during maintenance window
4. Verify functionality
5. Monitor for issues

For support, contact: admin@einspot.com.ng