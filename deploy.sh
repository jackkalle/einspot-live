#!/bin/bash

# Production Deployment Script for EINSPOT SOLUTIONS
# This script handles the complete deployment process

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="einspot.com.ng"
EMAIL="admin@einspot.com.ng"
BACKUP_DIR="/opt/einspot-backups"
DEPLOY_DIR="/opt/einspot-app"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_requirements() {
    log_info "Checking system requirements..."
    
    # Check if running as root
    if [[ $EUID -eq 0 ]]; then
        log_error "This script should not be run as root for security reasons"
        exit 1
    fi
    
    # Check required commands
    local required_commands=("docker" "docker-compose" "git" "openssl")
    for cmd in "${required_commands[@]}"; do
        if ! command -v $cmd &> /dev/null; then
            log_error "$cmd is required but not installed"
            exit 1
        fi
    done
    
    # Check Docker daemon
    if ! docker info &> /dev/null; then
        log_error "Docker daemon is not running"
        exit 1
    fi
    
    log_success "System requirements check passed"
}

setup_directories() {
    log_info "Setting up directories..."
    
    # Create necessary directories
    sudo mkdir -p $DEPLOY_DIR
    sudo mkdir -p $BACKUP_DIR
    sudo mkdir -p /var/log/einspot
    sudo mkdir -p /opt/ssl
    
    # Set permissions
    sudo chown -R $USER:$USER $DEPLOY_DIR
    sudo chown -R $USER:$USER $BACKUP_DIR
    
    log_success "Directories setup completed"
}

setup_ssl() {
    log_info "Setting up SSL certificates..."
    
    if [[ ! -f "/opt/ssl/fullchain.pem" ]] || [[ ! -f "/opt/ssl/privkey.pem" ]]; then
        log_warning "SSL certificates not found. Setting up Let's Encrypt..."
        
        # Install certbot if not present
        if ! command -v certbot &> /dev/null; then
            log_info "Installing certbot..."
            sudo apt-get update
            sudo apt-get install -y certbot
        fi
        
        # Stop any running web servers
        sudo systemctl stop nginx apache2 2>/dev/null || true
        
        # Generate SSL certificate
        sudo certbot certonly --standalone \
            --email $EMAIL \
            --agree-tos \
            --no-eff-email \
            -d $DOMAIN \
            -d www.$DOMAIN
        
        # Copy certificates to our SSL directory
        sudo cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem /opt/ssl/
        sudo cp /etc/letsencrypt/live/$DOMAIN/privkey.pem /opt/ssl/
        sudo chown -R $USER:$USER /opt/ssl/
        
        # Setup auto-renewal
        (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet --post-hook 'docker-compose -f $DEPLOY_DIR/docker-compose.prod.yml restart nginx'") | crontab -
        
        log_success "SSL certificates setup completed"
    else
        log_info "SSL certificates already exist"
    fi
}

create_env_file() {
    log_info "Creating environment file..."
    
    if [[ ! -f "$DEPLOY_DIR/.env" ]]; then
        log_warning ".env file not found. Creating from template..."
        
        # Generate secure secrets
        JWT_SECRET=$(openssl rand -base64 48)
        MONGO_ROOT_PASSWORD=$(openssl rand -base64 32)
        MONGO_USER_PASSWORD=$(openssl rand -base64 32)
        ENCRYPTION_KEY=$(openssl rand -base64 24)
        
        cat > $DEPLOY_DIR/.env << EOF
# Production Environment Variables - Generated $(date)
DOMAIN=$DOMAIN
FRONTEND_URL=https://$DOMAIN
BACKEND_URL=https://$DOMAIN/api

# Database Configuration
MONGODB_URI=mongodb://einspot_user:$MONGO_USER_PASSWORD@mongodb:27017/einspot_db?authSource=einspot_db
MONGO_ROOT_USERNAME=root
MONGO_ROOT_PASSWORD=$MONGO_ROOT_PASSWORD

# Security
JWT_SECRET=$JWT_SECRET
ENCRYPTION_KEY=$ENCRYPTION_KEY

# Email Configuration (CONFIGURE THESE)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password
FROM_EMAIL=noreply@$DOMAIN

# Analytics (CONFIGURE THIS)
GA_MEASUREMENT_ID=G-XXXXXXXXXX

# Payment Gateways (CONFIGURE THESE)
PAYSTACK_SECRET_KEY=sk_live_your_paystack_secret_key
PAYSTACK_PUBLIC_KEY=pk_live_your_paystack_public_key
FLUTTERWAVE_SECRET_KEY=FLWSECK_LIVE-your_flutterwave_secret_key
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_LIVE-your_flutterwave_public_key

# Monitoring
SENTRY_DSN=https://your_sentry_dsn@sentry.io/project_id
LOG_LEVEL=info

# SSL Configuration
SSL_EMAIL=$EMAIL
EOF
        
        log_warning "Please edit $DEPLOY_DIR/.env and configure the required services (email, analytics, payments)"
        log_warning "Press Enter when you have configured the .env file..."
        read
    else
        log_info "Environment file already exists"
    fi
}

backup_existing() {
    log_info "Creating backup of existing deployment..."
    
    if [[ -d "$DEPLOY_DIR" ]] && [[ "$(ls -A $DEPLOY_DIR)" ]]; then
        local backup_name="einspot-backup-$(date +%Y%m%d-%H%M%S)"
        local backup_path="$BACKUP_DIR/$backup_name"
        
        mkdir -p $backup_path
        
        # Backup application files
        cp -r $DEPLOY_DIR/* $backup_path/ 2>/dev/null || true
        
        # Backup database
        if docker ps | grep -q mongodb; then
            log_info "Backing up database..."
            docker exec mongodb mongodump --out /tmp/backup
            docker cp mongodb:/tmp/backup $backup_path/mongodb-backup
        fi
        
        # Compress backup
        tar -czf $backup_path.tar.gz -C $BACKUP_DIR $backup_name
        rm -rf $backup_path
        
        log_success "Backup created: $backup_path.tar.gz"
    else
        log_info "No existing deployment to backup"
    fi
}

deploy_application() {
    log_info "Deploying application..."
    
    # Copy application files
    cp -r . $DEPLOY_DIR/
    cd $DEPLOY_DIR
    
    # Build and start services
    log_info "Building Docker images..."
    docker-compose -f docker-compose.prod.yml build --no-cache
    
    log_info "Starting services..."
    docker-compose -f docker-compose.prod.yml up -d
    
    # Wait for services to be ready
    log_info "Waiting for services to be ready..."
    sleep 30
    
    # Health check
    local max_attempts=30
    local attempt=1
    
    while [[ $attempt -le $max_attempts ]]; do
        if curl -f -s https://$DOMAIN/health > /dev/null; then
            log_success "Application is healthy and running"
            break
        else
            log_info "Attempt $attempt/$max_attempts: Waiting for application to be ready..."
            sleep 10
            ((attempt++))
        fi
    done
    
    if [[ $attempt -gt $max_attempts ]]; then
        log_error "Application failed to start properly"
        log_info "Checking logs..."
        docker-compose -f docker-compose.prod.yml logs --tail=50
        exit 1
    fi
}

setup_monitoring() {
    log_info "Setting up monitoring and maintenance..."
    
    # Create log rotation
    sudo tee /etc/logrotate.d/einspot << EOF
/var/log/einspot/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 $USER $USER
    postrotate
        docker-compose -f $DEPLOY_DIR/docker-compose.prod.yml restart backend
    endscript
}
EOF
    
    # Setup backup cron job
    (crontab -l 2>/dev/null; echo "0 2 * * * $DEPLOY_DIR/backup.sh") | crontab -
    
    # Setup health check monitoring
    (crontab -l 2>/dev/null; echo "*/5 * * * * curl -f https://$DOMAIN/health || echo 'Health check failed' | mail -s 'EINSPOT Health Alert' $EMAIL") | crontab -
    
    log_success "Monitoring setup completed"
}

cleanup() {
    log_info "Cleaning up..."
    
    # Remove old Docker images
    docker image prune -f
    
    # Remove old backups (keep last 7 days)
    find $BACKUP_DIR -name "einspot-backup-*.tar.gz" -mtime +7 -delete
    
    log_success "Cleanup completed"
}

main() {
    log_info "Starting EINSPOT SOLUTIONS production deployment..."
    
    check_requirements
    setup_directories
    setup_ssl
    create_env_file
    backup_existing
    deploy_application
    setup_monitoring
    cleanup
    
    log_success "Deployment completed successfully!"
    log_info "Application is now running at: https://$DOMAIN"
    log_info "Admin panel: https://$DOMAIN/admin"
    log_info "API documentation: https://$DOMAIN/api/docs"
    
    log_warning "Post-deployment checklist:"
    echo "1. Configure email settings in .env file"
    echo "2. Set up payment gateway credentials"
    echo "3. Configure Google Analytics"
    echo "4. Test all functionality"
    echo "5. Set up monitoring alerts"
    echo "6. Configure backup storage (AWS S3)"
}

# Run main function
main "$@"