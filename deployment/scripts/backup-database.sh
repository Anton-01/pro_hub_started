#!/bin/bash

################################################################################
# Database Backup Script for Pro Hub API
#
# This script creates automated backups of PostgreSQL database
#
# Usage:
#   ./backup-database.sh
#
# Setup as cron job:
#   crontab -e
#   0 2 * * * /path/to/backup-database.sh >> /var/log/backup.log 2>&1
################################################################################

set -e

# Configuration
BACKUP_DIR="/home/deployer/backups"
DB_NAME="pro_hub_db"
DB_USER="pro_hub_user"
DB_HOST="localhost"
DB_PORT="5432"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

function print_success {
    echo -e "${GREEN}✅ $1${NC}"
}

function print_info {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

function print_error {
    echo -e "${RED}❌ $1${NC}"
}

echo "======================================"
echo "Database Backup - $(date)"
echo "======================================"
echo ""

# Create backup directory if it doesn't exist
if [ ! -d "$BACKUP_DIR" ]; then
    print_info "Creating backup directory: $BACKUP_DIR"
    mkdir -p "$BACKUP_DIR"
fi

# Backup filename
BACKUP_FILE="$BACKUP_DIR/backup_${DATE}.sql.gz"

# Check if .pgpass exists
if [ ! -f ~/.pgpass ]; then
    print_error "~/.pgpass file not found!"
    print_info "Create it with: echo 'localhost:5432:$DB_NAME:$DB_USER:YOUR_PASSWORD' > ~/.pgpass"
    print_info "Then run: chmod 600 ~/.pgpass"
    exit 1
fi

# Perform backup
print_info "Starting backup of database: $DB_NAME"
if pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" "$DB_NAME" | gzip > "$BACKUP_FILE"; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    print_success "Backup completed: $BACKUP_FILE ($BACKUP_SIZE)"
else
    print_error "Backup failed!"
    exit 1
fi
echo ""

# Clean old backups
print_info "Cleaning backups older than $RETENTION_DAYS days..."
OLD_BACKUPS=$(find "$BACKUP_DIR" -name "backup_*.sql.gz" -mtime +$RETENTION_DAYS)

if [ -n "$OLD_BACKUPS" ]; then
    echo "$OLD_BACKUPS" | while read -r file; do
        rm -f "$file"
        print_info "Deleted: $(basename $file)"
    done
    print_success "Old backups cleaned"
else
    print_info "No old backups to clean"
fi
echo ""

# List current backups
print_info "Current backups:"
ls -lh "$BACKUP_DIR"/backup_*.sql.gz 2>/dev/null || echo "No backups found"
echo ""

# Calculate total backup size
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)
print_info "Total backup size: $TOTAL_SIZE"
echo ""

print_success "Backup process completed successfully!"
echo ""
