# 📚 Deployment Documentation Index

Índice completo de toda la documentación de deployment para Pro Hub API.

---

## 🚀 Getting Started

**Nuevo en deployment?** Empieza aquí:

1. 📖 **[README.md](README.md)** - Guía completa de deployment paso a paso
2. ✅ **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - Checklist completa

---

## 📁 Estructura de Archivos

```
deployment/
├── README.md                     # Guía principal de deployment
├── DEPLOYMENT_CHECKLIST.md       # Checklist completa
├── GITHUB_SECRETS.md             # Configuración de secrets para CI/CD
├── INDEX.md                      # Este archivo
│
├── nginx/
│   └── pro_hub.conf              # Configuración de Nginx
│
├── supervisor/
│   ├── horizon.conf              # Supervisor config para producción
│   └── horizon-staging.conf      # Supervisor config para staging
│
├── scripts/
│   ├── server-setup.sh           # Setup automatizado del servidor
│   ├── deploy.sh                 # Script de deployment manual
│   └── backup-database.sh        # Script de backup automático
│
└── frontend-examples/
    ├── README.md                 # Guía de integración frontend
    ├── graphql-client.ts         # Cliente GraphQL
    ├── auth.ts                   # Utilidades de autenticación
    └── .env.example              # Variables de entorno
```

---

## 📖 Documentación por Tema

### 🔧 Configuración del Servidor

| Documento | Descripción | Tiempo Est. |
|-----------|-------------|-------------|
| [README.md § Configuración Inicial del Servidor](README.md#configuración-inicial-del-servidor) | Setup completo de Ubuntu 24.04 con LEPP stack | 30-45 min |
| [scripts/server-setup.sh](scripts/server-setup.sh) | Script automatizado de instalación | 10-15 min |

### 🚀 Deployment

| Documento | Descripción | Uso |
|-----------|-------------|-----|
| [README.md § Deployment Manual](README.md#deployment-manual) | Deployment sin CI/CD | Para deployments manuales |
| [scripts/deploy.sh](scripts/deploy.sh) | Script de deployment automatizado | `./deploy.sh main` |
| [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) | Checklist completa de verificación | Antes de cada deploy |

### 🔄 CI/CD

| Documento | Descripción | Implementación |
|-----------|-------------|----------------|
| [README.md § Configuración de CI/CD](README.md#configuración-de-cicd) | Setup de GitHub Actions | Una vez |
| [GITHUB_SECRETS.md](GITHUB_SECRETS.md) | Todos los secrets necesarios | Referencia rápida |
| [.github/workflows/deploy-production.yml](../.github/workflows/deploy-production.yml) | Workflow de producción | Auto con push a main |
| [.github/workflows/deploy-staging.yml](../.github/workflows/deploy-staging.yml) | Workflow de staging | Auto con push a develop |

### 🌐 Configuración de Servicios

| Archivo | Descripción | Ubicación en Servidor |
|---------|-------------|-----------------------|
| [nginx/pro_hub.conf](nginx/pro_hub.conf) | Nginx web server config | `/etc/nginx/sites-available/pro_hub` |
| [supervisor/horizon.conf](supervisor/horizon.conf) | Laravel Horizon queue worker | `/etc/supervisor/conf.d/horizon.conf` |
| [supervisor/horizon-staging.conf](supervisor/horizon-staging.conf) | Horizon para staging | `/etc/supervisor/conf.d/horizon-staging.conf` |

### 🔐 Seguridad

| Documento | Contenido |
|-----------|-----------|
| [README.md § Checklist de Seguridad Final](README.md#checklist-de-seguridad-final) | 20+ items de seguridad |
| [GITHUB_SECRETS.md § Seguridad](GITHUB_SECRETS.md#seguridad) | Buenas prácticas para secrets |

### 💾 Backups

| Documento | Descripción |
|-----------|-------------|
| [scripts/backup-database.sh](scripts/backup-database.sh) | Backup automático de PostgreSQL |
| [README.md § Configurar Backups Automáticos](README.md#paso-9-configurar-backups-automáticos) | Setup de backups diarios |

### 🌐 Frontend

| Documento | Descripción |
|-----------|-------------|
| [frontend-examples/README.md](frontend-examples/README.md) | Guía completa de integración |
| [frontend-examples/graphql-client.ts](frontend-examples/graphql-client.ts) | Cliente GraphQL production-ready |
| [frontend-examples/auth.ts](frontend-examples/auth.ts) | Autenticación JWT |
| [README.md § Frontend: Recomendaciones para Vercel](README.md#frontend-recomendaciones-para-vercel) | Configuración de Vercel |

---

## 🎯 Quick Reference

### Comandos Comunes

```bash
# Deployment manual
./deployment/scripts/deploy.sh main

# Ver logs
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/pro_hub_error.log

# Reiniciar servicios
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo supervisorctl restart horizon

# Backup manual
./deployment/scripts/backup-database.sh

# Modo mantenimiento
php artisan down --retry=60 --secret="tu-secret"
php artisan up

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan config:cache
```

### URLs Importantes

- **API Production:** https://api.tu-dominio.com
- **GraphQL Endpoint:** https://api.tu-dominio.com/graphql
- **Frontend:** https://app.tu-dominio.com
- **DigitalOcean Dashboard:** https://cloud.digitalocean.com/

### Archivos de Configuración

| Archivo | Ubicación |
|---------|-----------|
| Application `.env` | `/var/www/pro_hub_started/.env` |
| Nginx config | `/etc/nginx/sites-available/pro_hub` |
| Supervisor config | `/etc/supervisor/conf.d/horizon.conf` |
| PostgreSQL `.pgpass` | `~/.pgpass` |

---

## 🔍 Troubleshooting

### Problemas Comunes

| Error | Solución Rápida | Documento |
|-------|-----------------|-----------|
| 502 Bad Gateway | Verificar PHP-FPM: `sudo systemctl restart php8.3-fpm` | [README § Troubleshooting](README.md#troubleshooting) |
| CORS Error | Verificar Nginx config y FRONTEND_URL | [README § Troubleshooting](README.md#error-cors-policy) |
| Connection refused | Verificar PostgreSQL: `sudo systemctl status postgresql` | [README § Troubleshooting](README.md#error-connection-refused-a-postgresql) |
| Horizon no procesa | Reiniciar: `sudo supervisorctl restart horizon` | [README § Troubleshooting](README.md#horizon-no-procesa-jobs) |
| GitHub Actions falla | Verificar secrets y SSH key | [GITHUB_SECRETS § Troubleshooting](GITHUB_SECRETS.md#troubleshooting) |

---

## 📊 Workflow Visual

```
┌─────────────────────────────────────────────────────────────┐
│                   DEPLOYMENT WORKFLOW                       │
└─────────────────────────────────────────────────────────────┘

1. DESARROLLO LOCAL
   ├─ Escribir código
   ├─ Tests locales
   └─ Commit & Push
       │
       ↓
2. GITHUB ACTIONS (CI/CD)
   ├─ Run Tests (PHPUnit + PostgreSQL + Redis)
   ├─ Security Audit
   └─ Deploy to Server via SSH
       │
       ↓
3. VPS SERVER
   ├─ Pull código
   ├─ composer install
   ├─ php artisan migrate
   ├─ Cache & Optimize
   └─ Restart services
       │
       ↓
4. LIVE
   ├─ API: https://api.tu-dominio.com
   └─ Frontend: https://app.tu-dominio.com
```

---

## 📞 Recursos Externos

### Documentación Oficial

- **Laravel:** https://laravel.com/docs/11.x
- **Lighthouse GraphQL:** https://lighthouse-php.com/
- **Horizon:** https://laravel.com/docs/11.x/horizon
- **PostgreSQL:** https://www.postgresql.org/docs/15/
- **Nginx:** https://nginx.org/en/docs/
- **DigitalOcean:** https://docs.digitalocean.com/

### Herramientas Recomendadas

- **Monitoring:** https://uptimerobot.com (gratis)
- **Error Tracking:** https://sentry.io (gratis hasta 5k eventos/mes)
- **Logs:** https://papertrailapp.com (gratis hasta 50MB/mes)
- **SSL Check:** https://www.ssllabs.com/ssltest/

---

## 📝 Actualizaciones

### Version History

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0.0 | 2026-01-12 | Documentación inicial completa |

---

## 🤝 Contribuciones

Para mejorar esta documentación:

1. Editar archivos en `/deployment/`
2. Commit con mensaje descriptivo
3. Push al repositorio

---

## 📧 Soporte

**Preguntas o problemas?**

1. Revisar [README § Troubleshooting](README.md#troubleshooting)
2. Buscar en logs del servidor
3. Revisar GitHub Actions logs
4. Contactar al equipo de DevOps

---

**Última actualización:** 2026-01-12
**Mantenido por:** DevOps Team
