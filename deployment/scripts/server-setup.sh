#!/bin/bash

################################################################################
# Server Setup Script for Pro Hub API
# This script sets up a fresh Ubuntu 24.04 server with LEPP stack
#
# Usage:
#   chmod +x server-setup.sh
#   ./server-setup.sh
#
# Requirements:
#   - Ubuntu 24.04 LTS
#   - Root or sudo access
################################################################################

set -e

echo "======================================"
echo "Pro Hub API - Server Setup"
echo "======================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

function print_success {
    echo -e "${GREEN}✅ $1${NC}"
}

function print_info {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

function print_error {
    echo -e "${RED}❌ $1${NC}"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please do not run this script as root. Run as a sudo user instead."
    exit 1
fi

# Update system
print_info "Updating system packages..."
sudo apt update && sudo apt upgrade -y
print_success "System updated"
echo ""

# Set timezone
print_info "Setting timezone to America/Mexico_City..."
sudo timedatectl set-timezone America/Mexico_City
print_success "Timezone set"
echo ""

# Install Nginx
print_info "Installing Nginx..."
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
print_success "Nginx installed and started"
echo ""

# Install PostgreSQL 15
print_info "Installing PostgreSQL 15..."
sudo apt install postgresql postgresql-contrib -y
sudo systemctl enable postgresql
sudo systemctl start postgresql
print_success "PostgreSQL installed and started"
echo ""

# Install PHP 8.3
print_info "Installing PHP 8.3 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-pgsql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-curl \
    php8.3-zip \
    php8.3-intl \
    php8.3-redis \
    php8.3-gd \
    php8.3-imagick
print_success "PHP 8.3 installed"
echo ""

# Configure PHP
print_info "Configuring PHP..."
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.3/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.3/fpm/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.3/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.3/fpm/php.ini
sudo systemctl restart php8.3-fpm
print_success "PHP configured"
echo ""

# Install Redis
print_info "Installing Redis..."
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
print_success "Redis installed and started"
echo ""

# Install Composer
print_info "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
print_success "Composer installed"
echo ""

# Install Node.js 20 LTS
print_info "Installing Node.js 20 LTS..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y
print_success "Node.js installed ($(node -v))"
echo ""

# Install Supervisor
print_info "Installing Supervisor..."
sudo apt install supervisor -y
sudo systemctl enable supervisor
sudo systemctl start supervisor
print_success "Supervisor installed and started"
echo ""

# Install Git
print_info "Installing Git..."
sudo apt install git -y
print_success "Git installed ($(git --version))"
echo ""

# Install additional utilities
print_info "Installing additional utilities..."
sudo apt install -y curl wget unzip vim htop ufw fail2ban
print_success "Utilities installed"
echo ""

# Configure Firewall
print_info "Configuring UFW firewall..."
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
echo "y" | sudo ufw enable
print_success "Firewall configured"
echo ""

# Configure Fail2ban
print_info "Configuring Fail2ban..."
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
print_success "Fail2ban configured"
echo ""

# Create project directory
print_info "Creating project directory..."
sudo mkdir -p /var/www
sudo chown $USER:www-data /var/www
print_success "Project directory created"
echo ""

# Database setup prompt
echo ""
echo "======================================"
echo "Database Setup"
echo "======================================"
echo ""
print_info "Now you need to set up PostgreSQL database."
echo ""
read -p "Enter database name (default: pro_hub_db): " DB_NAME
DB_NAME=${DB_NAME:-pro_hub_db}

read -p "Enter database user (default: pro_hub_user): " DB_USER
DB_USER=${DB_USER:-pro_hub_user}

read -sp "Enter database password: " DB_PASSWORD
echo ""

print_info "Creating PostgreSQL database and user..."
sudo -u postgres psql << EOF
CREATE DATABASE $DB_NAME;
CREATE USER $DB_USER WITH ENCRYPTED PASSWORD '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;
ALTER DATABASE $DB_NAME OWNER TO $DB_USER;
EOF
print_success "Database created"
echo ""

# Save database credentials
cat > ~/database_credentials.txt << EOF
Database Name: $DB_NAME
Database User: $DB_USER
Database Password: $DB_PASSWORD
EOF
chmod 600 ~/database_credentials.txt
print_success "Database credentials saved to ~/database_credentials.txt"
echo ""

# Install Certbot for SSL
print_info "Installing Certbot for SSL certificates..."
sudo apt install certbot python3-certbot-nginx -y
print_success "Certbot installed"
echo ""

echo ""
echo "======================================"
echo "✅ Server Setup Complete!"
echo "======================================"
echo ""
echo "Next steps:"
echo "1. Clone your repository to /var/www/pro_hub_started"
echo "2. Copy Nginx config: sudo cp deployment/nginx/pro_hub.conf /etc/nginx/sites-available/pro_hub"
echo "3. Enable site: sudo ln -s /etc/nginx/sites-available/pro_hub /etc/nginx/sites-enabled/"
echo "4. Copy Supervisor config: sudo cp deployment/supervisor/horizon.conf /etc/supervisor/conf.d/"
echo "5. Configure .env file with database credentials"
echo "6. Run: composer install"
echo "7. Run: php artisan migrate --seed"
echo "8. Setup SSL: sudo certbot --nginx -d api.tu-dominio.com"
echo ""
echo "Database credentials saved in: ~/database_credentials.txt"
echo ""
