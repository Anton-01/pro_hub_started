# Guia de Instalacion - macOS

Esta guia detalla todos los pasos necesarios para configurar y ejecutar el Panel Empresarial en un entorno macOS.

---

## Tabla de Contenidos

1. [Requisitos del Sistema](#1-requisitos-del-sistema)
2. [Instalacion de Herramientas](#2-instalacion-de-herramientas)
3. [Configuracion del Proyecto](#3-configuracion-del-proyecto)
4. [Ejecucion con Docker](#4-ejecucion-con-docker)
5. [Comandos Utiles](#5-comandos-utiles)
6. [Solucion de Problemas](#6-solucion-de-problemas)
7. [Desarrollo sin Docker](#7-desarrollo-sin-docker-opcional)

---

## 1. Requisitos del Sistema

### Hardware Minimo
- **Procesador:** Intel Core i5 / Apple M1 o superior
- **RAM:** 8 GB minimo (16 GB recomendado)
- **Almacenamiento:** 10 GB libres

### Versiones de macOS Soportadas
- macOS Monterey (12.0) o superior
- macOS Ventura (13.0)
- macOS Sonoma (14.0)

---

## 2. Instalacion de Herramientas

### 2.1 Instalar Homebrew

Homebrew es el gestor de paquetes para macOS. Abre Terminal y ejecuta:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Despues de la instalacion, agrega Homebrew al PATH (para Apple Silicon):

```bash
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

Verifica la instalacion:

```bash
brew --version
```

### 2.2 Instalar Git

```bash
brew install git
```

Configura tu identidad:

```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

### 2.3 Instalar Docker Desktop

#### Opcion A: Descarga directa (Recomendado)

1. Visita [Docker Desktop para Mac](https://www.docker.com/products/docker-desktop/)
2. Descarga la version para tu procesador:
   - **Apple Silicon (M1/M2/M3):** Docker Desktop for Mac with Apple Silicon
   - **Intel:** Docker Desktop for Mac with Intel chip
3. Abre el archivo `.dmg` y arrastra Docker a Aplicaciones
4. Abre Docker Desktop desde Aplicaciones
5. Acepta los terminos y completa la configuracion inicial

#### Opcion B: Via Homebrew

```bash
brew install --cask docker
```

#### Configuracion de Docker Desktop

1. Abre Docker Desktop
2. Ve a **Settings** (engranaje)
3. En **Resources > Advanced**:
   - CPUs: 4 o mas
   - Memory: 6 GB o mas
   - Swap: 2 GB
   - Disk image size: 60 GB
4. Haz clic en **Apply & Restart**

Verifica la instalacion:

```bash
docker --version
docker compose version
```

### 2.4 Instalar Herramientas Adicionales (Opcional)

Para desarrollo sin Docker:

```bash
# PHP 8.4
brew install php@8.4
echo 'export PATH="/opt/homebrew/opt/php@8.4/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Composer
brew install composer

# Node.js y npm
brew install node@20
echo 'export PATH="/opt/homebrew/opt/node@20/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# PostgreSQL (cliente)
brew install libpq
echo 'export PATH="/opt/homebrew/opt/libpq/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Redis (cliente)
brew install redis
```

---

## 3. Configuracion del Proyecto

### 3.1 Clonar el Repositorio

```bash
cd ~/Projects  # o tu directorio preferido
git clone https://github.com/tu-usuario/pro_hub_started.git
cd pro_hub_started
```

### 3.2 Configurar Variables de Entorno

Copia el archivo de ejemplo:

```bash
cp .env.example .env
```

Edita el archivo `.env` con los valores para Docker:

```bash
# Abre con tu editor preferido
nano .env
# o
code .env  # si tienes VS Code
```

Asegurate de que estas variables esten configuradas:

```env
# Base de datos
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=panel_empresarial
DB_USERNAME=postgres
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Mail
MAIL_HOST=mailpit
MAIL_PORT=1025

# Puertos (opcional, modifica si hay conflictos)
APP_PORT=80
PGADMIN_PORT=5050
MAILPIT_PORT=8025
```

### 3.3 Generar Clave de Aplicacion

Esto se hara automaticamente al iniciar Docker, pero puedes generarla manualmente:

```bash
# Dentro del contenedor (ver seccion 4)
docker compose exec app php artisan key:generate
```

---

## 4. Ejecucion con Docker

### 4.1 Construir e Iniciar Contenedores

Primera vez (construye las imagenes):

```bash
docker compose build
```

Iniciar todos los servicios:

```bash
docker compose up -d
```

Ver el estado de los contenedores:

```bash
docker compose ps
```

Deberias ver algo como:

```
NAME              STATUS          PORTS
panel_app         Up (healthy)    9000/tcp
panel_nginx       Up              0.0.0.0:80->80/tcp
panel_pgsql       Up (healthy)    0.0.0.0:5432->5432/tcp
panel_redis       Up (healthy)    0.0.0.0:6379->6379/tcp
panel_horizon     Up
panel_mailpit     Up              0.0.0.0:8025->8025/tcp
panel_pgadmin     Up              0.0.0.0:5050->80/tcp
```

### 4.2 Configuracion Inicial

Ejecuta las migraciones:

```bash
docker compose exec app php artisan migrate
```

(Opcional) Ejecuta los seeders:

```bash
docker compose exec app php artisan db:seed
```

Genera la clave JWT:

```bash
docker compose exec app php artisan jwt:secret
```

### 4.3 Compilar Assets Frontend

Opcion A: Compilacion unica

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

Opcion B: Modo desarrollo con hot reload

```bash
docker compose --profile development up -d node
```

### 4.4 URLs de Acceso

| Servicio | URL |
|----------|-----|
| Aplicacion | http://localhost |
| Panel Admin | http://localhost/admin |
| pgAdmin | http://localhost:5050 |
| Mailpit | http://localhost:8025 |
| Horizon | http://localhost/horizon |

### Credenciales por Defecto

**pgAdmin:**
- Email: admin@panel.local
- Password: admin

**Conexion a PostgreSQL desde pgAdmin:**
- Host: pgsql
- Port: 5432
- Database: panel_empresarial
- Username: postgres
- Password: secret

---

## 5. Comandos Utiles

### Gestion de Contenedores

```bash
# Iniciar servicios
docker compose up -d

# Detener servicios
docker compose down

# Detener y eliminar volumenes (CUIDADO: borra datos)
docker compose down -v

# Reiniciar un servicio especifico
docker compose restart app

# Ver logs de todos los servicios
docker compose logs -f

# Ver logs de un servicio especifico
docker compose logs -f app

# Reconstruir un contenedor
docker compose build --no-cache app
docker compose up -d app
```

### Comandos Laravel (dentro del contenedor)

```bash
# Acceder al shell del contenedor
docker compose exec app sh

# Ejecutar comandos Artisan
docker compose exec app php artisan <comando>

# Ejemplos comunes
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:list
docker compose exec app php artisan tinker

# Ejecutar tests
docker compose exec app php artisan test

# Composer
docker compose exec app composer install
docker compose exec app composer update

# npm
docker compose exec app npm install
docker compose exec app npm run dev
docker compose exec app npm run build
```

### Alias Utiles (agregar a ~/.zshrc)

```bash
# Agrega estas lineas a tu ~/.zshrc
alias dc="docker compose"
alias dce="docker compose exec"
alias dcl="docker compose logs -f"
alias art="docker compose exec app php artisan"

# Luego ejecuta
source ~/.zshrc

# Ahora puedes usar:
dc up -d
art migrate
art cache:clear
```

---

## 6. Solucion de Problemas

### Puerto 80 en uso

Si el puerto 80 esta ocupado:

```bash
# Ver que proceso usa el puerto
sudo lsof -i :80

# Cambiar el puerto en .env
APP_PORT=8080
```

Luego accede a `http://localhost:8080`

### Error de conexion a PostgreSQL

```bash
# Verificar que el contenedor este corriendo
docker compose ps pgsql

# Ver logs del contenedor
docker compose logs pgsql

# Reiniciar el servicio
docker compose restart pgsql
```

### Permisos en storage/

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www:www storage bootstrap/cache
```

### Docker no inicia (Apple Silicon)

Si tienes problemas con Docker en M1/M2/M3:

1. Asegurate de tener Rosetta 2 instalado:
   ```bash
   softwareupdate --install-rosetta
   ```

2. En Docker Desktop > Settings > General:
   - Activa "Use Rosetta for x86/amd64 emulation on Apple Silicon"

### Contenedor app no inicia

```bash
# Ver logs detallados
docker compose logs app

# Verificar Dockerfile
docker compose build --no-cache app

# Limpiar cache de Docker
docker system prune -a
```

### Error "Class not found"

```bash
docker compose exec app composer dump-autoload
docker compose exec app php artisan optimize:clear
```

### Redis no conecta

```bash
# Verificar conexion
docker compose exec redis redis-cli ping
# Deberia responder: PONG

# Ver logs
docker compose logs redis
```

---

## 7. Desarrollo sin Docker (Opcional)

Si prefieres desarrollar sin Docker:

### Requisitos

Asegurate de tener instalado:
- PHP 8.4 con extensiones: pdo_pgsql, redis, gd, zip, intl, mbstring
- Composer 2.x
- Node.js 20.x y npm
- PostgreSQL 16
- Redis 7

### Instalacion

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Configurar .env
cp .env.example .env

# Editar .env con valores locales
# DB_HOST=127.0.0.1
# REDIS_HOST=127.0.0.1

# Generar clave
php artisan key:generate

# Crear base de datos
createdb panel_empresarial

# Ejecutar migraciones
php artisan migrate

# Iniciar servidor de desarrollo
composer dev
```

Este comando inicia:
- Servidor PHP en http://localhost:8000
- Vite para assets
- Cola de trabajos
- Logs en tiempo real

---

## Actualizaciones

Para actualizar el proyecto:

```bash
git pull origin main

docker compose exec app composer install
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run build

docker compose restart app horizon
```

---

## Soporte

Si encuentras problemas:

1. Revisa los logs: `docker compose logs -f`
2. Consulta la documentacion en `/docs`
3. Abre un issue en el repositorio

---

*Ultima actualizacion: Diciembre 2025*
