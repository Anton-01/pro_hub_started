# Panel Empresarial - Communication Hub

Sistema de comunicación multi-tenant desarrollado con Laravel 12 y GraphQL.

## Tecnologías

- **Backend:** Laravel 12
- **API:** GraphQL (Lighthouse)
- **Base de datos:** PostgreSQL
- **Cache/Colas:** Redis
- **Autenticación:** Laravel Sanctum + JWT
- **Colas:** Laravel Horizon
- **Email (Dev):** Mailpit

## Requisitos

### Con Docker (Recomendado)
- Docker Desktop 4.0+
- Docker Compose 2.0+

### Sin Docker
- PHP 8.2+
- Composer 2.0+
- PostgreSQL 15+
- Redis 7+
- Node.js 18+ y npm

---

## Instalación con Docker (Laravel Sail)

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd pro_hub_started
```

### 2. Instalar dependencias (primera vez)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` con los siguientes valores para Docker:

```env
DB_HOST=pgsql
REDIS_HOST=redis
MAIL_HOST=mailpit
```

### 4. Iniciar los contenedores

```bash
./vendor/bin/sail up -d
```

### 5. Generar clave de aplicación

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Ejecutar migraciones

```bash
./vendor/bin/sail artisan migrate
```

### 7. (Opcional) Ejecutar seeders

```bash
./vendor/bin/sail artisan db:seed
```

### 8. Compilar assets frontend

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Comandos útiles de Sail

```bash
# Iniciar contenedores
./vendor/bin/sail up -d

# Detener contenedores
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Ejecutar comandos Artisan
./vendor/bin/sail artisan <comando>

# Ejecutar comandos Composer
./vendor/bin/sail composer <comando>

# Ejecutar comandos npm
./vendor/bin/sail npm <comando>

# Acceder al shell del contenedor
./vendor/bin/sail shell

# Ejecutar tests
./vendor/bin/sail test

# Iniciar Laravel Horizon (colas)
./vendor/bin/sail artisan horizon
```

### Servicios disponibles

| Servicio    | URL                           |
|-------------|-------------------------------|
| Aplicación  | http://localhost              |
| PostgreSQL  | localhost:5432                |
| Redis       | localhost:6379                |
| Mailpit     | http://localhost:8025         |
| Horizon     | http://localhost/horizon      |

---

## Instalación sin Docker

### 1. Clonar e instalar dependencias

```bash
git clone <repository-url>
cd pro_hub_started
composer install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Configura las credenciales de PostgreSQL y Redis en `.env`.

### 3. Crear base de datos PostgreSQL

```bash
createdb panel_empresarial
```

### 4. Ejecutar migraciones

```bash
php artisan migrate
```

### 5. Instalar dependencias frontend

```bash
npm install
npm run build
```

### 6. Iniciar servidor de desarrollo

```bash
composer dev
```

Este comando inicia concurrentemente:
- Servidor PHP (puerto 8000)
- Cola de trabajos
- Laravel Pail (logs)
- Vite (assets)

---

## API GraphQL

La API GraphQL está disponible en:

- **Endpoint:** `POST /graphql`
- **Playground:** `GET /graphql-playground` (solo en desarrollo)

### Autenticación

La API usa tokens Bearer (Sanctum). Incluye el token en el header:

```
Authorization: Bearer <token>
```

---

## Testing

```bash
# Con Docker
./vendor/bin/sail test

# Sin Docker
composer test
```

---

## Estructura del proyecto

```
app/
├── Console/        # Comandos Artisan
├── Events/         # Eventos del sistema
├── GraphQL/        # Resolvers y tipos GraphQL
├── Http/           # Controllers y Middleware
├── Jobs/           # Trabajos en cola
├── Listeners/      # Listeners de eventos
├── Mail/           # Clases de email
├── Models/         # Modelos Eloquent
├── Providers/      # Service Providers
├── Services/       # Servicios de negocio
└── Traits/         # Traits reutilizables

graphql/            # Schema GraphQL
database/
├── migrations/     # Migraciones
├── factories/      # Factories para testing
└── seeders/        # Seeders de datos
```

---

## Licencia

Este proyecto está bajo la licencia MIT.
