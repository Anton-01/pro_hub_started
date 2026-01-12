# 🚀 Pro Hub API - Deployment Guide

Guía completa para el deployment del backend Pro Hub API en VPS DigitalOcean con CI/CD usando GitHub Actions.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Configuración Inicial del Servidor](#configuración-inicial-del-servidor)
- [Deployment Manual](#deployment-manual)
- [Configuración de CI/CD](#configuración-de-cicd)
- [Configuración del Frontend](#configuración-del-frontend)
- [Mantenimiento](#mantenimiento)
- [Troubleshooting](#troubleshooting)

---

## 🔧 Requisitos Previos

### Para el Servidor

- **VPS:** DigitalOcean Droplet
  - OS: Ubuntu 24.04 LTS
  - RAM: 2 GB mínimo (4 GB recomendado)
  - CPU: 2 vCPUs
  - Storage: 50 GB SSD
  - Costo aproximado: $12-24/mes

- **Dominio:** Un dominio configurado con DNS apuntando al servidor
  - `api.tu-dominio.com` → IP del servidor (registro A)

### Para CI/CD

- **GitHub:** Acceso al repositorio con permisos de admin
- **SSH Key:** Para deployment automatizado

---

## 🛠️ Configuración Inicial del Servidor

### Paso 1: Crear Droplet en DigitalOcean

1. Login a DigitalOcean → **Create** → **Droplets**
2. Configuración:
   - **Imagen:** Ubuntu 24.04 LTS x64
   - **Plan:** Basic Shared CPU
   - **CPU:** Regular (2 GB RAM / 2 vCPUs) - $18/mes
   - **Datacenter:** Elegir más cercano a usuarios
   - **Authentication:** SSH Key (crear si no tienes)
   - **Hostname:** `pro-hub-api`
3. Crear Droplet y esperar ~60 segundos

### Paso 2: Configuración Inicial

```bash
# Conectar al servidor
ssh root@YOUR_DROPLET_IP

# Actualizar sistema
apt update && apt upgrade -y

# Crear usuario deployer
adduser deployer
usermod -aG sudo deployer

# Copiar SSH keys al nuevo usuario
rsync --archive --chown=deployer:deployer ~/.ssh /home/deployer

# Salir y reconectar como deployer
exit
ssh deployer@YOUR_DROPLET_IP
```

### Paso 3: Ejecutar Script de Setup Automatizado

```bash
# Clonar el repositorio temporalmente para obtener el script
git clone https://github.com/Anton-01/pro_hub_started.git /tmp/setup
cd /tmp/setup

# Ejecutar script de setup (instala LEPP stack completo)
chmod +x deployment/scripts/server-setup.sh
./deployment/scripts/server-setup.sh
```

El script instalará automáticamente:
- ✅ Nginx
- ✅ PostgreSQL 15
- ✅ PHP 8.3 + extensiones
- ✅ Redis
- ✅ Composer
- ✅ Node.js 20 LTS
- ✅ Supervisor
- ✅ UFW Firewall
- ✅ Fail2ban
- ✅ Certbot (Let's Encrypt)

**Tiempo estimado:** 10-15 minutos

### Paso 4: Clonar Repositorio de Producción

```bash
# Clonar repositorio en ubicación definitiva
cd /var/www
git clone https://github.com/Anton-01/pro_hub_started.git
cd pro_hub_started
git checkout main
```

### Paso 5: Configurar Aplicación

```bash
# Copiar archivo de entorno
cp .env.production.example .env

# Editar .env con tus credenciales reales
nano .env
```

**Variables críticas a configurar:**
```env
APP_URL=https://api.tu-dominio.com
FRONTEND_URL=https://app.tu-dominio.com
DB_DATABASE=pro_hub_db
DB_USERNAME=pro_hub_user
DB_PASSWORD=tu_password_seguro
MAIL_HOST=smtp.mailgun.org
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
```

```bash
# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Generar claves
php artisan key:generate
php artisan jwt:secret

# Crear enlace de storage
php artisan storage:link

# Ejecutar migraciones
php artisan migrate --force --seed

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan lighthouse:cache

# Permisos correctos
sudo chown -R deployer:www-data /var/www/pro_hub_started
sudo chmod -R 755 /var/www/pro_hub_started
sudo chmod -R 775 /var/www/pro_hub_started/storage
sudo chmod -R 775 /var/www/pro_hub_started/bootstrap/cache
```

### Paso 6: Configurar Nginx

```bash
# Copiar configuración
sudo cp deployment/nginx/pro_hub.conf /etc/nginx/sites-available/pro_hub

# Editar y reemplazar 'api.tu-dominio.com' con tu dominio real
sudo nano /etc/nginx/sites-available/pro_hub

# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/pro_hub /etc/nginx/sites-enabled/

# Deshabilitar sitio default
sudo rm /etc/nginx/sites-enabled/default

# Test de configuración
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

### Paso 7: Configurar SSL (Let's Encrypt)

```bash
# Obtener certificado SSL
sudo certbot --nginx -d api.tu-dominio.com

# Responder preguntas:
# Email: tu@email.com
# Terms: A (Agree)
# Share email: N
# Redirect HTTP to HTTPS: 2 (Yes)

# Verificar renovación automática
sudo certbot renew --dry-run
```

### Paso 8: Configurar Horizon (Queue Worker)

```bash
# Copiar configuración de Supervisor
sudo cp deployment/supervisor/horizon.conf /etc/supervisor/conf.d/

# Editar si la ruta es diferente
sudo nano /etc/supervisor/conf.d/horizon.conf

# Recargar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon

# Verificar status
sudo supervisorctl status horizon
```

### Paso 9: Configurar Backups Automáticos

```bash
# Crear archivo .pgpass para backups sin password
echo "localhost:5432:pro_hub_db:pro_hub_user:TU_PASSWORD" > ~/.pgpass
chmod 600 ~/.pgpass

# Test del script de backup
cd /var/www/pro_hub_started
./deployment/scripts/backup-database.sh

# Configurar cron job para backups diarios a las 2 AM
crontab -e
```

Agregar esta línea:
```
0 2 * * * /var/www/pro_hub_started/deployment/scripts/backup-database.sh >> /var/log/backup.log 2>&1
```

### Paso 10: Verificar Instalación

```bash
# Test de GraphQL endpoint
curl -X POST https://api.tu-dominio.com/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __typename }"}'

# Debería retornar: {"data":{"__typename":"Query"}}
```

🎉 **¡Servidor configurado exitosamente!**

---

## 📦 Deployment Manual

Para deployments manuales sin CI/CD:

```bash
# Conectar al servidor
ssh deployer@YOUR_SERVER_IP

# Navegar al directorio
cd /var/www/pro_hub_started

# Ejecutar script de deployment
./deployment/scripts/deploy.sh main
```

El script ejecutará automáticamente:
1. ✅ Pull del código más reciente
2. ✅ Instalación de dependencias
3. ✅ Modo de mantenimiento
4. ✅ Migraciones de base de datos
5. ✅ Limpieza de caché
6. ✅ Optimización de aplicación
7. ✅ Reinicio de servicios
8. ✅ Vuelta a modo online

---

## 🔄 Configuración de CI/CD

### Paso 1: Generar SSH Key para GitHub Actions

```bash
# En tu máquina local
ssh-keygen -t ed25519 -C "github-actions@pro-hub" -f ~/.ssh/github_actions

# Copiar la clave pública al servidor
ssh-copy-id -i ~/.ssh/github_actions.pub deployer@YOUR_SERVER_IP

# Mostrar clave privada (copiar COMPLETA incluyendo BEGIN/END)
cat ~/.ssh/github_actions
```

### Paso 2: Configurar Secrets en GitHub

1. Ir a tu repositorio en GitHub
2. **Settings** → **Secrets and variables** → **Actions**
3. Crear los siguientes secrets:

| Secret Name | Valor | Ejemplo |
|-------------|-------|---------|
| `SSH_PRIVATE_KEY` | Clave privada completa | `-----BEGIN OPENSSH...` |
| `SERVER_IP` | IP del servidor | `167.99.123.45` |
| `SERVER_USER` | Usuario SSH | `deployer` |
| `APP_PATH` | Ruta de la aplicación | `/var/www/pro_hub_started` |
| `APP_URL` | URL de la API | `https://api.tu-dominio.com` |
| `MAINTENANCE_SECRET` | Token secreto | `random_string_here` |

### Paso 3: Configurar SUDOERS (para reiniciar servicios sin password)

```bash
# En el servidor
sudo visudo
```

Agregar al final:
```
# Allow deployer to restart services without password
deployer ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl restart horizon
deployer ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl status horizon
deployer ALL=(ALL) NOPASSWD: /bin/systemctl restart php8.3-fpm
deployer ALL=(ALL) NOPASSWD: /bin/systemctl restart nginx
```

### Paso 4: Test de CI/CD

```bash
# En tu máquina local, hacer un commit y push a main
git add .
git commit -m "Test CI/CD deployment"
git push origin main
```

GitHub Actions ejecutará automáticamente:
1. ✅ Tests (PHPUnit con PostgreSQL y Redis)
2. ✅ Security audit
3. ✅ Deployment al servidor
4. ✅ Health check

Ver el progreso en: `GitHub → Actions tab`

---

## 🌐 Configuración del Frontend

### Frontend en Vercel (Astro)

**Recomendación:** Mantener en Vercel (gratis, CDN global, SSL automático)

#### Paso 1: Configurar Variables de Entorno en Vercel

1. Dashboard Vercel → Tu proyecto → **Settings** → **Environment Variables**
2. Agregar:

```env
PUBLIC_GRAPHQL_ENDPOINT=https://api.tu-dominio.com/graphql
NODE_ENV=production
```

#### Paso 2: Modificar Cliente GraphQL

Crear `/src/lib/graphql-client.ts`:

```typescript
import { GraphQLClient } from 'graphql-request';

const endpoint = import.meta.env.PUBLIC_GRAPHQL_ENDPOINT ||
  'https://api.tu-dominio.com/graphql';

export const client = new GraphQLClient(endpoint, {
  credentials: 'include',
  mode: 'cors',
});

export function setAuthToken(token: string) {
  client.setHeader('Authorization', `Bearer ${token}`);
}

export function clearAuthToken() {
  client.setHeader('Authorization', '');
}
```

#### Paso 3: Actualizar Auth Utils

Ver ejemplos completos en: `/deployment/frontend-examples/`

#### Paso 4: Deploy en Vercel

```bash
# Push a GitHub
git add .
git commit -m "Connect to production API"
git push origin main
```

Vercel desplegará automáticamente.

---

## 🔧 Mantenimiento

### Ver Logs

```bash
# Laravel logs
tail -f /var/www/pro_hub_started/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/pro_hub_access.log
sudo tail -f /var/log/nginx/pro_hub_error.log

# Horizon logs
sudo tail -f /var/www/pro_hub_started/storage/logs/horizon.log

# PostgreSQL logs
sudo tail -f /var/log/postgresql/postgresql-15-main.log
```

### Comandos Útiles

```bash
# Reiniciar servicios
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo supervisorctl restart horizon

# Ver status de servicios
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo supervisorctl status horizon

# Limpiar caché de aplicación
php artisan cache:clear
php artisan config:clear

# Ver jobs en cola
php artisan horizon:list

# Modo de mantenimiento
php artisan down --retry=60
php artisan up
```

### Backups Manuales

```bash
# Ejecutar backup manualmente
/var/www/pro_hub_started/deployment/scripts/backup-database.sh

# Ver backups existentes
ls -lh ~/backups/

# Restaurar backup
gunzip -c ~/backups/backup_20260112_020000.sql.gz | psql -U pro_hub_user -d pro_hub_db
```

---

## 🐛 Troubleshooting

### Error: "502 Bad Gateway"

**Causa:** PHP-FPM no está corriendo o hay error en la aplicación.

**Solución:**
```bash
# Verificar PHP-FPM
sudo systemctl status php8.3-fpm

# Ver logs
sudo tail -f /var/log/nginx/pro_hub_error.log
tail -f /var/www/pro_hub_started/storage/logs/laravel.log

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Error: "CORS policy"

**Causa:** Nginx no está configurado correctamente para CORS.

**Solución:**
```bash
# Editar configuración de Nginx
sudo nano /etc/nginx/sites-available/pro_hub

# Verificar líneas CORS (líneas 47-70)
# Asegurar que FRONTEND_URL coincide con el origen

# Test y reiniciar
sudo nginx -t
sudo systemctl restart nginx
```

### Error: "Connection refused" a PostgreSQL

**Causa:** PostgreSQL no está corriendo o credenciales incorrectas.

**Solución:**
```bash
# Verificar PostgreSQL
sudo systemctl status postgresql

# Verificar credenciales en .env
nano /var/www/pro_hub_started/.env

# Verificar conexión manual
psql -U pro_hub_user -d pro_hub_db -h localhost
```

### Horizon no procesa jobs

**Causa:** Supervisor no está corriendo Horizon.

**Solución:**
```bash
# Verificar status
sudo supervisorctl status horizon

# Reiniciar
sudo supervisorctl restart horizon

# Ver logs
tail -f /var/www/pro_hub_started/storage/logs/horizon.log

# Si no existe el programa
sudo supervisorctl reread
sudo supervisorctl update
```

### GitHub Actions falla en deployment

**Causas comunes:**
1. SSH key incorrecta
2. Secrets mal configurados
3. Permisos de SUDOERS faltantes

**Solución:**
```bash
# Test de conexión SSH desde local
ssh -i ~/.ssh/github_actions deployer@YOUR_SERVER_IP

# Verificar sudoers
sudo visudo -c

# Ver logs de GitHub Actions en:
# GitHub → Actions → Último workflow → Logs
```

---

## 📊 Monitoring Recomendado

### Herramientas Gratuitas

1. **UptimeRobot** - https://uptimerobot.com
   - Monitoreo de uptime cada 5 minutos
   - Alertas por email/SMS

2. **Sentry** - https://sentry.io
   - Error tracking en tiempo real
   - Plan gratis: 5,000 eventos/mes

3. **Papertrail** - https://papertrailapp.com
   - Agregación de logs
   - Plan gratis: 50 MB/mes

### Configurar Sentry (Opcional)

```bash
# Instalar Sentry SDK
composer require sentry/sentry-laravel

# Publicar config
php artisan sentry:publish --dsn=YOUR_SENTRY_DSN

# Agregar a .env
SENTRY_LARAVEL_DSN=https://xxx@xxx.ingest.sentry.io/xxx
SENTRY_TRACES_SAMPLE_RATE=0.2
```

---

## 📞 Soporte

**Documentación Laravel:** https://laravel.com/docs/11.x
**Documentación Lighthouse:** https://lighthouse-php.com/
**DigitalOcean Docs:** https://docs.digitalocean.com/

---

## 📝 Changelog

### 2026-01-12
- ✅ Configuración inicial de deployment
- ✅ Scripts automatizados
- ✅ CI/CD con GitHub Actions
- ✅ Documentación completa

---

**¡Happy Deploying! 🚀**
