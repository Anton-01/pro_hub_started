# 🚀 Deployment Checklist

Usa esta checklist para asegurar un deployment exitoso del Pro Hub API.

---

## 📋 Pre-Deployment

### Código y Configuración

- [ ] Todas las features están completas y testeadas localmente
- [ ] Tests están pasando (`php artisan test`)
- [ ] Migraciones están creadas y probadas
- [ ] Seeders están actualizados (si aplica)
- [ ] `.env.production.example` está actualizado con todas las variables necesarias
- [ ] Código está commiteado y pusheado a GitHub
- [ ] Branch correcta seleccionada (main/production)

### Servidor

- [ ] Droplet creado en DigitalOcean (o servidor VPS alternativo)
- [ ] Dominio configurado y DNS propagado
  - [ ] Registro A: `api.tu-dominio.com` → IP del servidor
- [ ] SSH access configurado
- [ ] Usuario `deployer` creado con permisos sudo

---

## 🛠️ Configuración del Servidor

### Stack LEPP

- [ ] Script de setup ejecutado (`./deployment/scripts/server-setup.sh`)
- [ ] Nginx instalado y corriendo
- [ ] PostgreSQL 15 instalado y corriendo
- [ ] PHP 8.3 + extensiones instalados
- [ ] Redis instalado y corriendo
- [ ] Composer instalado
- [ ] Node.js 20 LTS instalado
- [ ] Supervisor instalado y corriendo
- [ ] Git instalado

### Base de Datos

- [ ] Base de datos creada
- [ ] Usuario de base de datos creado con password seguro
- [ ] Permisos otorgados al usuario
- [ ] Credenciales guardadas de forma segura

### Firewall y Seguridad

- [ ] UFW configurado y activo
  - [ ] Puerto 22 (SSH) permitido
  - [ ] Puertos 80/443 (HTTP/HTTPS) permitidos
- [ ] Fail2ban instalado y activo
- [ ] SSH configurado para denegar login root
- [ ] SSH configurado solo con keys (opcional pero recomendado)

---

## 📦 Deployment de la Aplicación

### Código

- [ ] Repositorio clonado en `/var/www/pro_hub_started`
- [ ] Branch correcta (main) checked out
- [ ] Dependencias de Composer instaladas (`composer install --no-dev`)

### Configuración

- [ ] Archivo `.env` creado desde `.env.production.example`
- [ ] Todas las variables de entorno configuradas:
  - [ ] `APP_KEY` generado (`php artisan key:generate`)
  - [ ] `JWT_SECRET` generado (`php artisan jwt:secret`)
  - [ ] `APP_URL` configurado con dominio real
  - [ ] `FRONTEND_URL` configurado
  - [ ] `DB_*` variables configuradas
  - [ ] `MAIL_*` variables configuradas
  - [ ] `REDIS_*` variables configuradas
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `LIGHTHOUSE_CACHE_ENABLE=true`
  - [ ] `GRAPHQL_PLAYGROUND_ENABLED=false`

### Base de Datos

- [ ] Migraciones ejecutadas (`php artisan migrate --force`)
- [ ] Seeders ejecutados si es necesario (`php artisan db:seed --force`)
- [ ] Verificar datos con `psql` o query directo

### Storage y Permisos

- [ ] Storage link creado (`php artisan storage:link`)
- [ ] Permisos correctos:
  ```bash
  sudo chown -R deployer:www-data /var/www/pro_hub_started
  sudo chmod -R 755 /var/www/pro_hub_started
  sudo chmod -R 775 /var/www/pro_hub_started/storage
  sudo chmod -R 775 /var/www/pro_hub_started/bootstrap/cache
  ```

### Optimización

- [ ] Config cacheada (`php artisan config:cache`)
- [ ] Routes cacheadas (`php artisan route:cache`)
- [ ] Views cacheadas (`php artisan view:cache`)
- [ ] Events cacheados (`php artisan event:cache`)
- [ ] GraphQL schema cacheado (`php artisan lighthouse:cache`)

---

## 🌐 Configuración de Web Server

### Nginx

- [ ] Archivo de configuración copiado a `/etc/nginx/sites-available/pro_hub`
- [ ] Archivo editado con dominio real (reemplazar `api.tu-dominio.com`)
- [ ] Archivo editado con frontend URL real en CORS
- [ ] Symlink creado en `/etc/nginx/sites-enabled/`
- [ ] Sitio default deshabilitado
- [ ] Test de configuración pasando (`sudo nginx -t`)
- [ ] Nginx reiniciado (`sudo systemctl restart nginx`)

### SSL Certificate

- [ ] Certbot instalado
- [ ] Certificado SSL obtenido (`sudo certbot --nginx -d api.tu-dominio.com`)
- [ ] Redirect HTTP → HTTPS configurado
- [ ] Test de renovación automática (`sudo certbot renew --dry-run`)

---

## 🔄 Queue Workers (Horizon)

- [ ] Configuración de Supervisor copiada a `/etc/supervisor/conf.d/horizon.conf`
- [ ] Ruta de aplicación correcta en archivo de config
- [ ] Usuario correcto en archivo de config (`deployer`)
- [ ] Supervisor recargado (`sudo supervisorctl reread && sudo supervisorctl update`)
- [ ] Horizon iniciado (`sudo supervisorctl start horizon`)
- [ ] Status verificado (`sudo supervisorctl status horizon`)

---

## 🔁 Backups

- [ ] Script de backup copiado o accesible
- [ ] Archivo `.pgpass` creado con credenciales
- [ ] Permisos correctos en `.pgpass` (600)
- [ ] Test manual de backup ejecutado
- [ ] Cron job configurado para backups diarios
- [ ] Directorio de backups verificado (`~/backups/`)

---

## 🔐 CI/CD (GitHub Actions)

### SSH Keys

- [ ] Key SSH generada para GitHub Actions
- [ ] Clave pública copiada al servidor (`~/.ssh/authorized_keys`)
- [ ] Test de conexión con clave privada exitoso

### GitHub Secrets

Todos los secrets configurados en `Settings → Secrets and variables → Actions`:

- [ ] `SSH_PRIVATE_KEY` - Clave privada completa
- [ ] `SERVER_IP` - IP del servidor
- [ ] `SERVER_USER` - Usuario SSH (deployer)
- [ ] `APP_PATH` - `/var/www/pro_hub_started`
- [ ] `APP_URL` - `https://api.tu-dominio.com`
- [ ] `MAINTENANCE_SECRET` - Token aleatorio (opcional)

### SUDOERS

- [ ] Configuración de sudoers actualizada para permitir comandos sin password:
  ```bash
  sudo visudo
  # Agregar líneas para deployer
  ```

### Workflows

- [ ] Archivo `.github/workflows/deploy-production.yml` presente
- [ ] Archivo `.github/workflows/deploy-staging.yml` presente (si aplica)
- [ ] Test push a main ejecutado
- [ ] Workflow ejecutándose sin errores
- [ ] Deployment exitoso verificado

---

## 🌐 Frontend (Vercel)

### Variables de Entorno en Vercel

- [ ] `PUBLIC_GRAPHQL_ENDPOINT` configurado
- [ ] `NODE_ENV=production` configurado

### Código

- [ ] Cliente GraphQL actualizado (`src/lib/graphql-client.ts`)
- [ ] Auth utils actualizados (`src/lib/auth.ts`)
- [ ] Imports actualizados en componentes
- [ ] Archivos mock eliminados o movidos (opcional)
- [ ] Variables de entorno en `.env` (desarrollo local)

### Deployment

- [ ] Código commiteado y pusheado
- [ ] Vercel desplegado automáticamente
- [ ] Frontend accesible
- [ ] Login funcional con backend real
- [ ] Queries GraphQL funcionando

---

## ✅ Testing Post-Deployment

### API Backend

- [ ] **Health Check:**
  ```bash
  curl https://api.tu-dominio.com/graphql
  # Debe retornar respuesta GraphQL
  ```

- [ ] **GraphQL Introspection:**
  ```bash
  curl -X POST https://api.tu-dominio.com/graphql \
    -H "Content-Type: application/json" \
    -d '{"query":"{ __typename }"}'
  # Debe retornar: {"data":{"__typename":"Query"}}
  ```

- [ ] **Login Mutation:**
  ```bash
  curl -X POST https://api.tu-dominio.com/graphql \
    -H "Content-Type: application/json" \
    -d '{
      "query":"mutation { login(email:\"test@test.com\", password:\"password\", companyId:\"uuid\") { access_token } }"
    }'
  # Debe retornar token o error de credenciales
  ```

### Frontend

- [ ] Página de login carga correctamente
- [ ] Dropdown de empresas carga desde backend
- [ ] Login con credenciales válidas funciona
- [ ] Redirect a dashboard después de login
- [ ] Dashboard carga datos desde backend
- [ ] Logout funciona correctamente
- [ ] Token se refresca automáticamente

### Services

- [ ] Nginx corriendo: `sudo systemctl status nginx`
- [ ] PHP-FPM corriendo: `sudo systemctl status php8.3-fpm`
- [ ] PostgreSQL corriendo: `sudo systemctl status postgresql`
- [ ] Redis corriendo: `sudo systemctl status redis-server`
- [ ] Horizon corriendo: `sudo supervisorctl status horizon`

### Logs

- [ ] No hay errores en Laravel logs: `tail -f storage/logs/laravel.log`
- [ ] No hay errores en Nginx: `sudo tail -f /var/log/nginx/pro_hub_error.log`
- [ ] Horizon procesando jobs: `tail -f storage/logs/horizon.log`

---

## 📊 Monitoring (Opcional pero Recomendado)

- [ ] UptimeRobot configurado para ping cada 5 minutos
- [ ] Sentry instalado y configurado para error tracking
- [ ] Papertrail configurado para agregación de logs
- [ ] Email alerts configurados

---

## 📝 Documentación

- [ ] README actualizado con instrucciones de deployment
- [ ] Credenciales guardadas en gestor de passwords (1Password, LastPass, etc.)
- [ ] Documentación de API actualizada
- [ ] Runbook creado para equipo

---

## 🎉 Post-Deployment

### Comunicación

- [ ] Equipo notificado del nuevo deployment
- [ ] Stakeholders informados
- [ ] URL de producción compartida

### Monitoring Inicial

- [ ] Monitorear logs durante primeras 24 horas
- [ ] Verificar uso de recursos (CPU, RAM, disco)
- [ ] Revisar métricas de Redis
- [ ] Revisar jobs procesados por Horizon

### Backup Verification

- [ ] Verificar que backup diario se ejecutó correctamente
- [ ] Test de restauración de backup (en ambiente de test)

---

## 🆘 Rollback Plan

Si algo sale mal:

### Opción 1: Rollback Manual

```bash
ssh deployer@YOUR_SERVER_IP
cd /var/www/pro_hub_started
git log --oneline -5  # Ver últimos commits
git reset --hard COMMIT_HASH  # Revertir a commit anterior
composer install --no-dev
php artisan migrate:rollback  # Si hubo migraciones problemáticas
php artisan config:cache
sudo supervisorctl restart horizon
sudo systemctl restart php8.3-fpm
```

### Opción 2: Rollback via GitHub

```bash
# Localmente
git revert COMMIT_HASH
git push origin main
# GitHub Actions deployará la versión revertida automáticamente
```

### Opción 3: Restaurar Backup

```bash
# En el servidor
cd ~
gunzip -c backups/backup_TIMESTAMP.sql.gz | psql -U pro_hub_user -d pro_hub_db
```

---

## 📞 Contactos de Emergencia

Mantén esta lista actualizada:

- **DevOps Lead:** [Nombre] - [Email] - [Teléfono]
- **Backend Lead:** [Nombre] - [Email] - [Teléfono]
- **DigitalOcean Support:** support@digitalocean.com
- **DNS Provider:** [Provider] - [Support URL]

---

## ✅ Sign-Off

- [ ] **Backend Developer:** ________________ Fecha: ______
- [ ] **DevOps:** ________________ Fecha: ______
- [ ] **QA:** ________________ Fecha: ______
- [ ] **Project Manager:** ________________ Fecha: ______

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Version/Commit:** _______________

---

**¡Deployment Completo! 🎉**
