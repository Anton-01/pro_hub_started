# GitHub Secrets Configuration

Esta es la lista completa de secrets que necesitas configurar en GitHub para que funcione el CI/CD.

## 📍 Ubicación

`GitHub Repository → Settings → Secrets and variables → Actions → Repository secrets`

## 🔑 Secrets Requeridos

### Para Producción (deploy-production.yml)

| Secret Name | Descripción | Ejemplo | Obligatorio |
|-------------|-------------|---------|-------------|
| `SSH_PRIVATE_KEY` | Clave privada SSH completa para acceder al servidor | `-----BEGIN OPENSSH PRIVATE KEY-----\n...` | ✅ Sí |
| `SERVER_IP` | Dirección IP del servidor VPS | `167.99.123.45` | ✅ Sí |
| `SERVER_USER` | Usuario SSH (típicamente `deployer`) | `deployer` | ✅ Sí |
| `APP_PATH` | Ruta absoluta de la aplicación en el servidor | `/var/www/pro_hub_started` | ✅ Sí |
| `APP_URL` | URL pública de la API | `https://api.tu-dominio.com` | ✅ Sí |
| `MAINTENANCE_SECRET` | Token secreto para acceder durante maintenance mode | `random_string_123456` | ⚠️ Opcional |

### Para Staging (deploy-staging.yml) - Si aplica

| Secret Name | Descripción | Ejemplo | Obligatorio |
|-------------|-------------|---------|-------------|
| `SSH_PRIVATE_KEY_STAGING` | Clave privada SSH para servidor staging | `-----BEGIN OPENSSH PRIVATE KEY-----\n...` | ✅ Sí (si usas staging) |
| `STAGING_SERVER_IP` | IP del servidor staging | `142.93.123.45` | ✅ Sí (si usas staging) |
| `STAGING_SERVER_USER` | Usuario SSH staging | `deployer` | ✅ Sí (si usas staging) |
| `STAGING_APP_PATH` | Ruta de app en staging | `/var/www/pro_hub_started_staging` | ✅ Sí (si usas staging) |
| `STAGING_APP_URL` | URL de staging | `https://staging-api.tu-dominio.com` | ✅ Sí (si usas staging) |

## 📝 Cómo Obtener los Valores

### 1. SSH_PRIVATE_KEY

```bash
# En tu máquina local, genera una nueva key SSH específica para GitHub Actions
ssh-keygen -t ed25519 -C "github-actions@pro-hub" -f ~/.ssh/github_actions

# Esto generará:
# ~/.ssh/github_actions       <- PRIVATE KEY (para GitHub Secret)
# ~/.ssh/github_actions.pub   <- PUBLIC KEY (para el servidor)

# Ver la clave PRIVADA completa (copiar TODO, incluyendo BEGIN/END)
cat ~/.ssh/github_actions

# Copiar la clave PÚBLICA al servidor
ssh-copy-id -i ~/.ssh/github_actions.pub deployer@YOUR_SERVER_IP

# O manualmente:
# ssh deployer@YOUR_SERVER_IP
# mkdir -p ~/.ssh
# nano ~/.ssh/authorized_keys
# Pegar contenido de github_actions.pub
# chmod 600 ~/.ssh/authorized_keys
```

**⚠️ IMPORTANTE:** La clave privada debe incluir:
- La línea `-----BEGIN OPENSSH PRIVATE KEY-----`
- Todo el contenido codificado
- La línea `-----END OPENSSH PRIVATE KEY-----`

### 2. SERVER_IP

```bash
# En tu máquina local, obtener IP del servidor
ssh deployer@YOUR_SERVER_IP "curl -s ifconfig.me"

# O desde DigitalOcean Dashboard:
# Droplets → tu-droplet → ipv4
```

### 3. SERVER_USER

Típicamente es `deployer` (el usuario que creaste durante el setup del servidor).

Si no recuerdas:
```bash
ssh root@YOUR_SERVER_IP
cat /etc/passwd | grep "/home" | cut -d: -f1
```

### 4. APP_PATH

```bash
# Desde el servidor
ssh deployer@YOUR_SERVER_IP
pwd  # Si estás en el directorio de la app

# O buscar:
find /var/www -name "artisan" -type f
```

Valor típico: `/var/www/pro_hub_started`

### 5. APP_URL

Esta es la URL pública de tu API configurada con tu dominio:
- Producción: `https://api.tu-dominio.com`
- Staging: `https://staging-api.tu-dominio.com`

### 6. MAINTENANCE_SECRET

Genera un string aleatorio:

```bash
# Opción 1: OpenSSL
openssl rand -hex 16

# Opción 2: Node
node -e "console.log(require('crypto').randomBytes(16).toString('hex'))"

# Opción 3: PHP
php -r "echo bin2hex(random_bytes(16));"

# Ejemplo de resultado: 5f4dcc3b5aa765d61d8327deb882cf99
```

Puedes usar este token para acceder al sitio durante mantenimiento:
```
https://api.tu-dominio.com?secret=5f4dcc3b5aa765d61d8327deb882cf99
```

## 🔧 Cómo Configurar en GitHub

### Método 1: UI de GitHub

1. Ve a tu repositorio en GitHub
2. Click en **Settings** (tab superior derecha)
3. En el menú izquierdo: **Secrets and variables** → **Actions**
4. Click en **New repository secret**
5. Ingresa:
   - **Name:** El nombre del secret (ej: `SSH_PRIVATE_KEY`)
   - **Secret:** El valor (asegúrate de copiar TODO, sin espacios extra)
6. Click **Add secret**
7. Repetir para cada secret

### Método 2: GitHub CLI (gh)

```bash
# Instalar GitHub CLI si no lo tienes
# https://cli.github.com/

# Login
gh auth login

# Configurar secrets
gh secret set SSH_PRIVATE_KEY < ~/.ssh/github_actions
gh secret set SERVER_IP -b"167.99.123.45"
gh secret set SERVER_USER -b"deployer"
gh secret set APP_PATH -b"/var/www/pro_hub_started"
gh secret set APP_URL -b"https://api.tu-dominio.com"
gh secret set MAINTENANCE_SECRET -b"$(openssl rand -hex 16)"
```

## ✅ Verificar Configuración

### 1. Listar secrets configurados

```bash
# Con GitHub CLI
gh secret list

# Debería mostrar:
# SSH_PRIVATE_KEY    Updated 2026-01-12
# SERVER_IP          Updated 2026-01-12
# SERVER_USER        Updated 2026-01-12
# APP_PATH           Updated 2026-01-12
# APP_URL            Updated 2026-01-12
# MAINTENANCE_SECRET Updated 2026-01-12
```

### 2. Test de conexión SSH

```bash
# Desde tu máquina local, test con la key de GitHub Actions
ssh -i ~/.ssh/github_actions deployer@YOUR_SERVER_IP "echo 'SSH works!'"

# Debería mostrar: SSH works!
```

Si esto funciona, GitHub Actions también podrá conectarse.

### 3. Test de workflow

Puedes hacer un push pequeño para probar:

```bash
git add .
git commit -m "Test CI/CD configuration"
git push origin main
```

Ve a: `GitHub → Actions tab` para ver el workflow ejecutándose.

## 🐛 Troubleshooting

### Error: "Permission denied (publickey)"

**Causa:** La clave SSH no está correctamente configurada.

**Solución:**
1. Verifica que copiaste la clave PRIVADA completa (con BEGIN/END)
2. Verifica que la clave PÚBLICA está en `~/.ssh/authorized_keys` del servidor
3. Verifica permisos: `chmod 600 ~/.ssh/authorized_keys` en el servidor

### Error: "Host key verification failed"

**Causa:** El servidor no está en known_hosts.

**Solución:** El workflow ya incluye `ssh-keyscan` para resolver esto, pero si persiste:

```bash
# Agregar manualmente el servidor a known_hosts de GitHub Actions
# Esto ya está incluido en el workflow (línea 116 de deploy-production.yml)
```

### Error: "sudo: no tty present and no askpass program specified"

**Causa:** El usuario no tiene permisos sudo sin password para los comandos necesarios.

**Solución:** Configurar sudoers en el servidor:

```bash
ssh deployer@YOUR_SERVER_IP
sudo visudo
```

Agregar al final:
```
deployer ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl restart horizon
deployer ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl status horizon
deployer ALL=(ALL) NOPASSWD: /bin/systemctl restart php8.3-fpm
deployer ALL=(ALL) NOPASSWD: /bin/systemctl restart nginx
```

## 📊 Secrets por Ambiente

### Desarrollo Local
No necesita secrets (usa .env local)

### Staging
- `SSH_PRIVATE_KEY_STAGING`
- `STAGING_SERVER_IP`
- `STAGING_SERVER_USER`
- `STAGING_APP_PATH`
- `STAGING_APP_URL`

### Producción
- `SSH_PRIVATE_KEY`
- `SERVER_IP`
- `SERVER_USER`
- `APP_PATH`
- `APP_URL`
- `MAINTENANCE_SECRET` (opcional)

## 🔒 Seguridad

### ✅ Buenas Prácticas

1. **Nunca commits secrets al repositorio**
   - Secrets en GitHub Secrets o .env (ignorado por git)
   - Verificar .gitignore incluye `.env*`

2. **Usa keys SSH específicas por ambiente**
   - Una key para staging
   - Otra key diferente para producción

3. **Revoca keys comprometidas inmediatamente**
   ```bash
   # En el servidor, remover de authorized_keys
   nano ~/.ssh/authorized_keys
   # Eliminar la línea de la key comprometida
   ```

4. **Rota secrets periódicamente**
   - Generar nuevas keys SSH cada 3-6 meses
   - Actualizar secrets en GitHub

5. **Limita permisos de deployment key**
   - La key solo debe tener acceso al servidor de deployment
   - No usar keys con acceso a múltiples servidores

## 📞 Referencias

- [GitHub Encrypted Secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [SSH Key Authentication](https://www.ssh.com/academy/ssh/public-key-authentication)
- [Best Practices for Secrets](https://docs.github.com/en/actions/security-guides/security-hardening-for-github-actions)

---

**Última actualización:** 2026-01-12
