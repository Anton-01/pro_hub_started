# Plan de Implementación - Panel Administrativo

Este documento describe las etapas para implementar el panel administrativo completo del Panel Empresarial.

---

## Resumen de Etapas

| Etapa | Descripción | Estado |
|-------|-------------|--------|
| 1 | Configuración Docker con imágenes independientes | Pendiente |
| 2 | Instalación y configuración de Bootstrap 5 (OneUI Style) | Pendiente |
| 3 | Rutas y controladores para panel administrativo | Pendiente |
| 4 | Vistas (Login, Dashboard, CRUDs completos) | Pendiente |
| 5 | Instrucciones de instalación para Mac | Pendiente |

---

## Etapa 1: Configuración Docker con Imágenes Independientes

### Objetivo
Crear una arquitectura Docker con contenedores independientes comunicándose a través de una red Docker dedicada.

### Contenedores a crear:
1. **app** - Aplicación Laravel (PHP 8.4 + Nginx)
2. **pgsql** - Base de datos PostgreSQL 16
3. **redis** - Cache y Colas (Redis 7 Alpine)
4. **horizon** - Worker de colas Laravel Horizon
5. **mailpit** - Servidor de correo para desarrollo
6. **node** - Compilación de assets (solo desarrollo)

### Archivos a generar:
- `docker-compose.yml` - Orquestación de servicios
- `docker-compose.prod.yml` - Configuración de producción
- `docker/app/Dockerfile` - Imagen PHP + Laravel
- `docker/app/nginx.conf` - Configuración Nginx
- `docker/app/supervisord.conf` - Supervisor para procesos
- `docker/app/php.ini` - Configuración PHP
- `docker/horizon/Dockerfile` - Worker de Horizon

---

## Etapa 2: Instalación Bootstrap 5 (Estilo OneUI)

### Objetivo
Integrar Bootstrap 5 con un diseño moderno inspirado en OneUI.

### Dependencias a instalar:
```json
{
  "bootstrap": "^5.3",
  "@popperjs/core": "^2.11",
  "sass": "^1.69",
  "@fortawesome/fontawesome-free": "^6.5"
}
```

### Estructura de assets:
```
resources/
├── sass/
│   ├── app.scss
│   ├── _variables.scss
│   ├── _sidebar.scss
│   ├── _header.scss
│   ├── _cards.scss
│   └── _forms.scss
├── js/
│   ├── app.js
│   ├── sidebar.js
│   └── helpers.js
└── views/
    └── layouts/
        ├── admin.blade.php
        └── auth.blade.php
```

---

## Etapa 3: Rutas y Controladores

### Rutas del Panel Admin (`routes/admin.php`):

```
/admin
├── /login                    # Auth\LoginController
├── /logout                   # Auth\LoginController
├── /dashboard                # DashboardController
├── /companies                # CompanyController (CRUD)
├── /users                    # UserController (CRUD)
├── /modules                  # ModuleController (CRUD)
├── /contacts                 # ContactController (CRUD)
├── /events                   # CalendarEventController (CRUD)
├── /news                     # NewsController (CRUD)
├── /banners                  # BannerImageController (CRUD)
├── /settings                 # SettingsController
└── /activity-logs            # ActivityLogController
```

### Controladores a crear:
1. `Admin/Auth/LoginController`
2. `Admin/DashboardController`
3. `Admin/CompanyController`
4. `Admin/UserController`
5. `Admin/ModuleController`
6. `Admin/ContactController`
7. `Admin/CalendarEventController`
8. `Admin/NewsController`
9. `Admin/BannerImageController`
10. `Admin/SettingsController`
11. `Admin/ActivityLogController`

---

## Etapa 4: Vistas del Panel Administrativo

### Estructura de vistas:

```
resources/views/
├── admin/
│   ├── layouts/
│   │   ├── app.blade.php          # Layout principal
│   │   └── guest.blade.php        # Layout para login
│   ├── partials/
│   │   ├── sidebar.blade.php      # Menú lateral
│   │   ├── header.blade.php       # Encabezado
│   │   ├── footer.blade.php       # Pie de página
│   │   └── alerts.blade.php       # Mensajes flash
│   ├── auth/
│   │   ├── login.blade.php        # Página de login
│   │   └── forgot-password.blade.php
│   ├── dashboard/
│   │   └── index.blade.php        # Dashboard principal
│   ├── companies/
│   │   ├── index.blade.php        # Listado
│   │   ├── create.blade.php       # Crear
│   │   ├── edit.blade.php         # Editar
│   │   └── show.blade.php         # Ver detalle
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── modules/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── contacts/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── events/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── news/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── banners/
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   ├── settings/
│   │   └── index.blade.php
│   └── activity-logs/
│       └── index.blade.php
```

### Componentes UI (Estilo OneUI):
- Cards con sombras suaves
- Sidebar colapsable con animaciones
- Header con dropdown de usuario
- Tablas con acciones inline
- Modales para confirmaciones
- Formularios con validación visual
- Dark mode toggle
- Breadcrumbs

---

## Etapa 5: Instrucciones para Mac

### Archivo: `SETUP_MAC.md`

Contenido:
1. Requisitos del sistema
2. Instalación de Homebrew
3. Instalación de Docker Desktop para Mac
4. Configuración del proyecto
5. Comandos de desarrollo
6. Solución de problemas comunes

---

## Orden de Ejecución Recomendado

1. **Etapa 1** - Docker (base de la infraestructura)
2. **Etapa 5** - Documentación Mac (para poder seguir los pasos)
3. **Etapa 2** - Assets Frontend (base visual)
4. **Etapa 3** - Rutas y Controladores (lógica)
5. **Etapa 4** - Vistas (interfaz completa)

---

## Notas Técnicas

### Autenticación Admin
- Sesiones web tradicionales (no API tokens)
- Middleware `auth:web` + verificación de rol admin
- Recordar sesión opcional
- Protección CSRF en todos los formularios

### Multi-tenancy
- Super Admin: Acceso a todas las empresas
- Admin: Solo acceso a su empresa
- Filtrado automático por company_id

### Seguridad
- Validación de datos en Form Requests
- Políticas de autorización (Policies)
- Rate limiting en login
- Logs de actividad automáticos

---

**¿Deseas que comience con alguna etapa específica?**
