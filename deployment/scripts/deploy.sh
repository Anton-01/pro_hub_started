#!/bin/bash

################################################################################
# Manual Deployment Script for Pro Hub API
#
# Usage:
#   ./deploy.sh [branch]
#
# Examples:
#   ./deploy.sh main      # Deploy main branch
#   ./deploy.sh develop   # Deploy develop branch
################################################################################

set -e

# Configuration
APP_PATH="/var/www/pro_hub_started"
BRANCH="${1:-main}"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

function print_success {
    echo -e "${GREEN}✅ $1${NC}"
}

function print_info {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

function print_warning {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

function print_error {
    echo -e "${RED}❌ $1${NC}"
}

function print_header {
    echo ""
    echo "======================================"
    echo "$1"
    echo "======================================"
    echo ""
}

# Check if running in correct directory
if [ ! -f "$APP_PATH/artisan" ]; then
    print_error "Not in Laravel application directory!"
    print_info "Please run this script from: $APP_PATH"
    exit 1
fi

print_header "🚀 Starting Deployment"
print_info "Branch: $BRANCH"
print_info "Path: $APP_PATH"
echo ""

# Confirm deployment
read -p "Continue with deployment? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Deployment cancelled"
    exit 0
fi

cd "$APP_PATH"

# Check for uncommitted changes
print_info "Checking for uncommitted changes..."
if [[ -n $(git status -s) ]]; then
    print_warning "You have uncommitted changes!"
    git status -s
    read -p "Continue anyway? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 0
    fi
fi
print_success "Git status checked"
echo ""

# Pull latest code
print_info "Pulling latest code from $BRANCH..."
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
print_success "Code updated"
echo ""

# Install/Update dependencies
print_info "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Dependencies installed"
echo ""

# Put app in maintenance mode
print_info "Enabling maintenance mode..."
php artisan down --retry=60 --secret="deploy-secret-$(date +%s)"
print_success "Maintenance mode enabled"
echo ""

# Run database migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"
echo ""

# Clear all caches
print_info "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
print_success "Caches cleared"
echo ""

# Optimize application
print_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan lighthouse:cache
print_success "Application optimized"
echo ""

# Set correct permissions
print_info "Setting permissions..."
sudo chown -R $USER:www-data "$APP_PATH"
sudo chmod -R 755 "$APP_PATH"
sudo chmod -R 775 "$APP_PATH/storage"
sudo chmod -R 775 "$APP_PATH/bootstrap/cache"
print_success "Permissions set"
echo ""

# Restart services
print_info "Restarting services..."

# Restart Horizon
if sudo supervisorctl status horizon > /dev/null 2>&1; then
    sudo supervisorctl restart horizon
    print_success "Horizon restarted"
else
    print_warning "Horizon not found in Supervisor"
fi

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
print_success "PHP-FPM restarted"

# Restart Nginx (optional)
# sudo systemctl restart nginx
# print_success "Nginx restarted"

echo ""

# Bring app back online
print_info "Disabling maintenance mode..."
php artisan up
print_success "Application is now live"
echo ""

# Show last commit
print_info "Current deployment:"
git log -1 --pretty=format:"Commit: %h%nAuthor: %an <%ae>%nDate: %ad%nMessage: %s%n" --date=short
echo ""

print_header "✅ Deployment Completed Successfully!"
print_success "Application deployed from branch: $BRANCH"
echo ""
