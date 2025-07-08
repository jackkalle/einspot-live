#!/bin/bash

# Backup Script for EINSPOT SOLUTIONS
# This script creates automated backups of the application and database

set -e

# Configuration
BACKUP_DIR="/opt/einspot-backups"
DEPLOY_DIR="/opt/einspot-app"
RETENTION_DAYS=30
AWS_S3_BUCKET="${AWS_S3_BUCKET:-einspot-backups}"
DATE=$(date +%Y%m%d-%H%M%S)
BACKUP_NAME="einspot-backup-$DATE"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}[BACKUP]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

create_backup() {
    log_info "Creating backup: $BACKUP_NAME"
    
    local backup_path="$BACKUP_DIR/$BACKUP_NAME"
    mkdir -p $backup_path
    
    # Backup application files
    log_info "Backing up application files..."
    tar -czf $backup_path/app-files.tar.gz -C $DEPLOY_DIR \
        --exclude='node_modules' \
        --exclude='*.log' \
        --exclude='.git' \
        .
    
    # Backup database
    log_info "Backing up database..."
    if docker ps | grep -q mongodb; then
        docker exec mongodb mongodump --out /tmp/backup --gzip
        docker cp mongodb:/tmp/backup $backup_path/mongodb-backup
        docker exec mongodb rm -rf /tmp/backup
    else
        log_warning "MongoDB container not running, skipping database backup"
    fi
    
    # Backup SSL certificates
    log_info "Backing up SSL certificates..."
    if [[ -d "/opt/ssl" ]]; then
        cp -r /opt/ssl $backup_path/
    fi
    
    # Create backup metadata
    cat > $backup_path/metadata.json << EOF
{
    "backup_name": "$BACKUP_NAME",
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "hostname": "$(hostname)",
    "app_version": "$(cd $DEPLOY_DIR && git rev-parse HEAD 2>/dev/null || echo 'unknown')",
    "database_included": $(docker ps | grep -q mongodb && echo 'true' || echo 'false'),
    "ssl_included": $([[ -d "/opt/ssl" ]] && echo 'true' || echo 'false')
}
EOF
    
    # Compress entire backup
    log_info "Compressing backup..."
    tar -czf $backup_path.tar.gz -C $BACKUP_DIR $BACKUP_NAME
    rm -rf $backup_path
    
    log_info "Backup created: $backup_path.tar.gz"
    echo "Backup size: $(du -h $backup_path.tar.gz | cut -f1)"
}

upload_to_s3() {
    if [[ -n "$AWS_ACCESS_KEY_ID" ]] && [[ -n "$AWS_SECRET_ACCESS_KEY" ]]; then
        log_info "Uploading backup to S3..."
        
        local backup_file="$BACKUP_DIR/$BACKUP_NAME.tar.gz"
        local s3_key="backups/$(date +%Y/%m)/$BACKUP_NAME.tar.gz"
        
        if command -v aws &> /dev/null; then
            aws s3 cp $backup_file s3://$AWS_S3_BUCKET/$s3_key
            log_info "Backup uploaded to S3: s3://$AWS_S3_BUCKET/$s3_key"
        else
            log_warning "AWS CLI not installed, skipping S3 upload"
        fi
    else
        log_warning "AWS credentials not configured, skipping S3 upload"
    fi
}

cleanup_old_backups() {
    log_info "Cleaning up old backups..."
    
    # Remove local backups older than retention period
    find $BACKUP_DIR -name "einspot-backup-*.tar.gz" -mtime +$RETENTION_DAYS -delete
    
    # Clean up S3 backups if configured
    if [[ -n "$AWS_ACCESS_KEY_ID" ]] && command -v aws &> /dev/null; then
        local cutoff_date=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d)
        aws s3 ls s3://$AWS_S3_BUCKET/backups/ --recursive | \
        awk '{print $1" "$2" "$4}' | \
        while read date time key; do
            if [[ "$date" < "$cutoff_date" ]]; then
                aws s3 rm s3://$AWS_S3_BUCKET/$key
                log_info "Removed old S3 backup: $key"
            fi
        done
    fi
    
    log_info "Cleanup completed"
}

verify_backup() {
    local backup_file="$BACKUP_DIR/$BACKUP_NAME.tar.gz"
    
    log_info "Verifying backup integrity..."
    
    if tar -tzf $backup_file > /dev/null 2>&1; then
        log_info "Backup verification successful"
        return 0
    else
        log_error "Backup verification failed"
        return 1
    fi
}

send_notification() {
    local status=$1
    local message=$2
    
    # Send email notification if configured
    if command -v mail &> /dev/null && [[ -n "$NOTIFICATION_EMAIL" ]]; then
        echo "$message" | mail -s "EINSPOT Backup $status" $NOTIFICATION_EMAIL
    fi
    
    # Log to syslog
    logger -t einspot-backup "$status: $message"
}

main() {
    log_info "Starting backup process..."
    
    # Create backup directory if it doesn't exist
    mkdir -p $BACKUP_DIR
    
    # Check available disk space
    local available_space=$(df $BACKUP_DIR | awk 'NR==2 {print $4}')
    local required_space=1048576  # 1GB in KB
    
    if [[ $available_space -lt $required_space ]]; then
        log_error "Insufficient disk space for backup"
        send_notification "FAILED" "Backup failed: Insufficient disk space"
        exit 1
    fi
    
    # Create backup
    if create_backup; then
        log_info "Backup creation successful"
    else
        log_error "Backup creation failed"
        send_notification "FAILED" "Backup creation failed"
        exit 1
    fi
    
    # Verify backup
    if verify_backup; then
        log_info "Backup verification successful"
    else
        log_error "Backup verification failed"
        send_notification "FAILED" "Backup verification failed"
        exit 1
    fi
    
    # Upload to S3
    upload_to_s3
    
    # Cleanup old backups
    cleanup_old_backups
    
    log_info "Backup process completed successfully"
    send_notification "SUCCESS" "Backup completed successfully: $BACKUP_NAME"
}

# Run main function
main "$@"