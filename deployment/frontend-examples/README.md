# Frontend Integration Examples

Archivos de ejemplo para integrar el frontend Astro con el backend Laravel en producción.

## 📋 Archivos Incluidos

- `graphql-client.ts` - Cliente GraphQL configurado para producción
- `auth.ts` - Utilidades de autenticación con JWT real
- `.env.example` - Variables de entorno para Vercel

## 🚀 Pasos de Integración

### 1. Instalar Dependencias

En el proyecto frontend (`home-page-ui`):

```bash
npm install graphql-request graphql
```

### 2. Copiar Archivos

```bash
# Copiar cliente GraphQL
cp deployment/frontend-examples/graphql-client.ts src/lib/

# REEMPLAZAR el archivo auth.ts existente
cp deployment/frontend-examples/auth.ts src/lib/
```

### 3. Configurar Variables de Entorno en Vercel

1. Ve a tu proyecto en Vercel Dashboard
2. **Settings** → **Environment Variables**
3. Agregar las siguientes variables:

```
PUBLIC_GRAPHQL_ENDPOINT = https://api.tu-dominio.com/graphql
NODE_ENV = production
```

### 4. Actualizar Imports en Componentes

Cualquier componente que use autenticación debe importar desde `lib/auth`:

```typescript
// Antes (con mock data)
import { login, logout } from '../lib/auth';

// Después (con backend real) - mismo import!
import { login, logout } from '../lib/auth';
```

La API es compatible, solo cambia la implementación interna.

### 5. Eliminar o Comentar Archivos Mock

Los siguientes archivos ya no son necesarios en producción:

```bash
# Estos archivos pueden ser eliminados o movidos a /src/data/mock/
src/data/users.json
src/data/modules.json
src/data/contacts.json
src/data/companies.json
src/data/events.json
src/data/news.json

# Estos archivos pueden ser eliminados
src/graphql/resolvers.ts  # Ya no usa resolvers locales
src/graphql/schema.ts     # Ya no usa schema local
src/pages/api/graphql.ts  # Opcional: eliminar si no se usa como proxy
```

**Nota:** Mantén estos archivos si quieres preservar una versión de desarrollo local.

### 6. Actualizar Queries GraphQL

Actualiza las queries en tus componentes para usar el cliente real:

```typescript
// Ejemplo: src/pages/index.astro

import { client } from '../lib/graphql-client';
import { gql } from 'graphql-request';
import { getStoredUser } from '../lib/auth';

// Verificar autenticación
const user = getStoredUser();

if (!user) {
  return Astro.redirect('/login');
}

// Query GraphQL
const DASHBOARD_QUERY = gql`
  query GetDashboardData($companyId: ID!) {
    modules(companyId: $companyId) {
      id
      title
      icon
      url
      order
    }
    newsItems(companyId: $companyId) {
      id
      title
      content
    }
    # ... más queries
  }
`;

try {
  const data = await client.request(DASHBOARD_QUERY, {
    companyId: user.company.id,
  });

  // Usar data.modules, data.newsItems, etc.
} catch (error) {
  console.error('Error loading dashboard:', error);
}
```

### 7. Actualizar Login Page

Actualiza `src/pages/login.astro` para usar las nuevas empresas:

```typescript
import { gql } from 'graphql-request';
import { client } from '../lib/graphql-client';

// Query para obtener empresas disponibles
const COMPANIES_QUERY = gql`
  query GetCompanies {
    companies {
      id
      name
      slug
    }
  }
`;

let companies = [];

try {
  const data = await client.request(COMPANIES_QUERY);
  companies = data.companies;
} catch (error) {
  console.error('Error loading companies:', error);
  companies = [];
}
```

### 8. Desplegar en Vercel

```bash
# Commit cambios
git add .
git commit -m "Integrate production GraphQL backend"
git push origin main
```

Vercel desplegará automáticamente. El frontend ahora usará el backend real.

## 🔐 Autenticación Flow

1. Usuario accede a `/login`
2. Selecciona empresa del dropdown (cargadas desde backend)
3. Ingresa email y password
4. Frontend envía mutation `login` al backend
5. Backend valida y retorna JWT tokens
6. Frontend guarda tokens en localStorage
7. Todas las queries subsecuentes usan el access token
8. Token se refresca automáticamente antes de expirar

## 🧪 Testing Local con Backend en Desarrollo

Si quieres testear localmente con el backend:

```bash
# En el backend Laravel
php artisan serve

# En .env.local del frontend
PUBLIC_GRAPHQL_ENDPOINT=http://localhost:8000/graphql
```

## 📝 Notas Importantes

### CORS

El backend ya está configurado para permitir peticiones desde:
- `https://home-page-ui.vercel.app`
- `https://app.tu-dominio.com`

Si usas otro dominio, actualiza en:
- Backend: `/deployment/nginx/pro_hub.conf` (línea 51)
- Backend: `.env` → `FRONTEND_URL`

### Seguridad

- ✅ Tokens JWT se almacenan en localStorage (seguro en HTTPS)
- ✅ Access tokens expiran en 60 minutos
- ✅ Refresh tokens expiran en 14 días
- ✅ Tokens se refrescan automáticamente
- ✅ CORS configurado para dominios específicos

### Performance

El backend usa Redis para cachear:
- Módulos: 10 minutos
- Contactos: 10 minutos
- Eventos: 10 minutos
- Noticias: 1 minuto
- Banner: 10 minutos
- Configuración: 1 hora

Los datos se actualizan automáticamente al hacer cambios desde el admin.

## 🐛 Troubleshooting

### Error: "CORS policy"

**Solución:** Verifica que `FRONTEND_URL` en el backend coincida con tu dominio de Vercel.

```bash
# En el servidor
nano /var/www/pro_hub_started/.env
# Cambiar FRONTEND_URL=https://tu-frontend.vercel.app
```

### Error: "Unauthorized" en todas las peticiones

**Solución:** Verifica que el token se esté guardando correctamente.

```javascript
// En console del navegador
console.log(localStorage.getItem('access_token'));
```

Si es null, hay un problema en el login. Verifica credenciales.

### Error: "Network request failed"

**Solución:** Verifica que el endpoint GraphQL esté accesible:

```bash
curl https://api.tu-dominio.com/graphql
```

Debe retornar un error GraphQL (no 404).

## 📞 Soporte

Ver documentación completa en: `/deployment/README.md`
