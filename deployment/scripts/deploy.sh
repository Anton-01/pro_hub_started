#!/bin/bash
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

function print_success { echo -e "${GREEN}✅ $1${NC}"; }
function print_info { echo -e "${BLUE}ℹ️  $1${NC}"; }
function print_warning { echo -e "${YELLOW}⚠️  $1${NC}"; }
function print_error { echo -e "${RED}❌ $1${NC}"; }
function print_header {
    echo -e "\n======================================\n$1\n======================================\n"
}

# Check if running in correct directory
if [ ! -f "$APP_PATH/artisan" ]; then
    print_error "Not in Laravel application directory!"
    exit 1
fi

print_header "🚀 Starting Professional Deployment"
print_info "Branch: $BRANCH"
print_info "Path: $APP_PATH"

# Confirm deployment
read -p "Continue with deployment? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Deployment cancelled"
    exit 0
fi

cd "$APP_PATH"

# 1. PRE-DEPLOYMENT PERMISSIONS (Evita errores de Git)
print_info "Ensuring correct permissions for Git..."
sudo chown -R $USER:www-data "$APP_PATH"
print_success "Permissions ready"

# 2. UPDATE CODE
print_info "Pulling latest code from $BRANCH..."
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
print_success "Code updated to $(git rev-parse --short HEAD)"

# 3. MAINTENANCE MODE
print_info "Enabling maintenance mode..."
php artisan down --retry=60 || print_warning "App already down"

# 4. BACKEND DEPENDENCIES & MIGRATIONS
print_info "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer updated"

print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# 5. FRONTEND ASSETS (Vite/Mix) - Crucial para ver cambios visuales
if [ -f "package.json" ]; then
    print_info "Compiling frontend assets (NPM)..."
    npm install
    npm run build
    print_success "Assets compiled successfully"
else
    print_warning "No package.json found, skipping NPM build"
fi

# 6. CACHE MANAGEMENT
print_info "Clearing and rebuilding caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
# Re-cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
if php artisan list | grep -q "lighthouse:cache"; then
    php artisan lighthouse:cache
fi
print_success "Caches optimized"

# 7. FINAL PERMISSIONS
print_info "Setting final storage permissions..."
sudo chmod -R 775 "$APP_PATH/storage"
sudo chmod -R 775 "$APP_PATH/bootstrap/cache"
print_success "Permissions finalized"

# 8. RESTART SERVICES
print_info "Restarting services..."
# Restart PHP-FPM (Limpia OPcache)
sudo systemctl restart php8.3-fpm
# Restart Horizon if exists
if sudo supervisorctl status horizon > /dev/null 2>&1; then
    sudo supervisorctl restart horizon
    print_success "Horizon restarted"
fi
# Nginx Restart (Opcional, pero recomendado si cambiaste algo de config)
sudo systemctl restart nginx
print_success "Web services restarted"

# 9. GO LIVE
print_info "Disabling maintenance mode..."
php artisan up
print_success "Application is now LIVE"

# 10. VERIFICATION SUMMARY
echo -e "\n${BLUE}--- DEPLOYMENT SUMMARY ---${NC}"
LAST_COMMIT_HASH=$(git rev-parse --short HEAD)
print_info "Current Hash: $LAST_COMMIT_HASH"
git log -1 --pretty=format:"Message: %s%nAuthor: %an%nDate: %ad" --date=short
echo -e "\n${YELLOW}Tip: If you don't see changes, check if local hash matches $LAST_COMMIT_HASH${NC}"
print_header "✅ Deployment Completed!"