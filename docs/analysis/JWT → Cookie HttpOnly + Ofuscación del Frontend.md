# Mejora de Seguridad: JWT → Cookie HttpOnly + Ofuscación del Frontend

## Contexto

Actualmente el sistema usa **Laravel Passport (JWT Bearer tokens)** donde:
- El token se guarda en `localStorage` (vulnerable a XSS).
- El frontend Angular envía el token en el header `Authorization: Bearer ...`.
- El frontend guarda todo el objeto de usuario en `localStorage`.
- Hay ~28 lugares distintos donde se lee `localStorage.getItem('currentUser')`.

El objetivo es migrar a **cookies HttpOnly** (el token nunca es accesible desde JS) y ofuscar el bundle de producción del frontend.

---

## User Review Required

> [!IMPORTANT]
> **CSRF Protection con SameSite**: Como el frontend (Angular en `ui.fase2spa.com`) y el API (`v2.fase2spa.com`) son **dominios distintos**, las cookies `SameSite=Strict` bloquearán las peticiones. Se debe usar `SameSite=Lax` con el header CSRF-Token en las mutaciones (POST/PUT/DELETE), o bien **servir el frontend y el API desde el mismo dominio** (recomendado para máxima seguridad).
>
> **¿Los dos proyectos están en el mismo dominio en producción?** Por favor confirma:
> - Opción A: `fase2spa.com` (frontend) y `v2.fase2spa.com` (API) → dominios distintos → necesitamos CSRF token explícito.
> - Opción B: Mismo dominio con rutas distintas (ej. `/` y `/api/`) → más simple y seguro.

> [!WARNING]
> **Sesiones activas**: Al migrar, todos los usuarios necesitarán re-autenticarse una vez porque el mecanismo cambia de Passport tokens a cookies de sesión.

---

## Open Questions

> [!IMPORTANT]
> **¿Se mantiene Laravel Passport o se cambia a Sanctum?**
> - **Laravel Sanctum** (recomendado) tiene soporte nativo para autenticación SPA con cookies HttpOnly. Es la solución oficial de Laravel para este caso.
> - **Laravel Passport** puede usarse con cookies, pero requiere más configuración custom.
> - El plan asume **migrar a Sanctum** ya que está diseñado exactamente para este caso (SPA con misma sesión/cookie). Passport seguirá instalado y puede convivir.

---

## Cambios Propuestos

### 📦 Backend (api.fase2 — Laravel)

---

#### [NEW] Instalar Laravel Sanctum
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Sanctum provee autenticación SPA mediante cookies de sesión con CSRF automático. Reemplaza el guard `auth:api` (Passport) por `auth:sanctum` en las rutas.

---

#### [MODIFY] [UserController.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Http/Controllers/UserController.php)

- `apiLogin()`: En lugar de devolver el token en el body, llamar a `Auth::login($user)` y dejar que Laravel genere la cookie de sesión.  
  - Responder `200 { success: { id, name, roles, ... } }` **sin token** en el body.
- Agregar `apiLogout()`: Llama a `Auth::logout()` e invalida la sesión.
- Agregar `apiMe()`: Endpoint para que el frontend verifique si hay sesión activa al recargar la página.

#### [MODIFY] [api.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/routes/api.php)

- Cambiar el middleware del grupo protegido de `auth:api` → `auth:sanctum`.
- Agregar rutas:
  - `POST /users/logout` (guest)
  - `GET /users/me` (sanctum)
- Ruta login: Agregar retorno de cookie de sesión en lugar de token.

#### [MODIFY] [config/auth.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/config/auth.php)

- Cambiar el guard default `api` de `passport` → `sanctum`.

#### [MODIFY] [config/cors.php] *(nuevo o actualizar)*

Sanctum requiere `supports_credentials: true` en la config de CORS:
```php
'supports_credentials' => true,
'allowed_origins' => ['https://ui.fase2spa.com.mx'],  // dominio exacto del frontend
```

#### [MODIFY] [.env](file:///Users/ruravi/workspace/fase2spa/api.fase2/.env)

Agregar:
```
SANCTUM_STATEFUL_DOMAINS=ui.fase2spa.com.mx,localhost:4200
SESSION_DOMAIN=.fase2spa.com.mx
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

### 🅰️ Frontend Angular (ui.fase2)

---

#### [MODIFY] [token.interceptor.ts](file:///Users/ruravi/workspace/fase2spa/ui.fase2/src/app/aunth/token.interceptor.ts)

- **Eliminar** el header `Authorization: Bearer ${token}`.
- **Agregar** `withCredentials: true` a todas las peticiones (para que las cookies se envíen automáticamente).
- Para endpoints de mutación (POST/PUT/DELETE), obtener el CSRF token con una petición previa a `/sanctum/csrf-cookie` y adjuntarlo como header `X-XSRF-TOKEN`.
- Angular's `HttpClient` con `withCredentials: true` y la cookie `XSRF-TOKEN` de Laravel lo maneja automáticamente.

#### [MODIFY] [user.service.ts](file:///Users/ruravi/workspace/fase2spa/ui.fase2/src/app/services/user.service.ts)

- `login()`: Ya no guarda nada en localStorage. El API establece la cookie.
- Agregar `logout()`: Llama `POST /api/users/logout`.
- Agregar `me()`: Llama `GET /api/users/me` para verificar sesión.
- `isLogin()`: En lugar de leer localStorage, consultar `me()` o usar una señal/BehaviorSubject con el estado de auth.
- `getToken()`: **Eliminar** este método, ya no es necesario.
- Agregar `currentUser$`: BehaviorSubject con los datos del usuario actual (cargados en memoria, no localStorage).

#### [MODIFY] [AlwaysAuthGuard.ts](file:///Users/ruravi/workspace/fase2spa/ui.fase2/src/app/aunth/AlwaysAuthGuard.ts)

- Cambiar de lectura de localStorage a verificar el `BehaviorSubject` del `UserService`.
- Si el usuario no está cargado en memoria, llamar `me()` para verificar cookie activa.

#### [MODIFY] Todos los servicios que leen `localStorage`

Los 16+ servicios que leen `localStorage.getItem('currentUser')` para obtener el usuario actual deben cambiarse a inyectar `UserService` y usar `userService.currentUser` en su lugar.

Archivos afectados:
- `role.service.ts`, `type.service.ts`, `package.service.ts`, `creditor.service.ts`,
- `pills_inventory.service.ts`, `client.service.ts`, `sale.service.ts`, `agent.service.ts`,
- `payment.service.ts`, `permission.service.ts`, `department.service.ts`, `purchase.service.ts`,
- `balance.service.ts`, `packages_tracking.service.ts`, `paginate.service.ts`, `products_inventory.service.ts`

#### [MODIFY] [login.component.ts](file:///Users/ruravi/workspace/fase2spa/ui.fase2/src/app/views/login/login.component.ts)

- Eliminar `localStorage.removeItem('currentUser')` y `localStorage.setItem(...)`.
- Después del login, llamar `userService.loadCurrentUser()` para cargar los datos en memoria.

#### [MODIFY] [default-layout.component.ts](file:///Users/ruravi/workspace/fase2spa/ui.fase2/src/app/containers/default-layout/default-layout.component.ts)

- Eliminar lectura de localStorage.
- Usar `userService.currentUser$` observable.
- El logout llama `userService.logout()` que invoca `POST /api/users/logout`.

#### [MODIFY] Vistas que leen localStorage directamente

- `sales.component.ts`, `box.component.ts`, `purchases.component.ts`, `schedule.component.ts`,
  `sale.component.ts`, `packages.component.ts`

Cambiar a usar `userService.currentUser` en lugar de parsear localStorage.

---

### 🔒 Ofuscación del Frontend para Producción

#### [MODIFY] [angular.json](file:///Users/ruravi/workspace/fase2spa/ui.fase2/angular.json)

En la configuración `production`, habilitar:
- `optimization: true` (ya es el default, pero lo hacemos explícito)
- **Source maps deshabilitados** (evitar que el código fuente sea visible en producción)
- Agregar `"sourceMap": false` en la config de producción.

Angular CLI en modo producción ya hace:
- **Tree-shaking y minificación** via esbuild
- **Mangling de nombres** de variables/funciones
- **Eliminación de dead code**

Para ofuscación adicional, podemos agregar un paso post-build con **javascript-obfuscator**.

#### [NEW] Script de build con ofuscación extra

Si se requiere ofuscación más agresiva (renombrar clases, strings cifrados):
```bash
npm install --save-dev javascript-obfuscator
```
Y agregar un script `build:prod:obfuscate` que corre el obfuscator sobre el output de `dist/`.

---

## Plan de Ejecución

### Fase 1 — Backend
1. Instalar Sanctum
2. Publicar config de Sanctum
3. Actualizar `UserController` (login, logout, me)
4. Actualizar rutas en `api.php`
5. Actualizar `config/auth.php`
6. Configurar CORS con `supports_credentials`
7. Actualizar `.env` con variables de Sanctum

### Fase 2 — Frontend
1. Actualizar `UserService` (BehaviorSubject, me(), logout(), sin localStorage)
2. Actualizar `TokenInterceptor` (withCredentials, sin Bearer token)
3. Actualizar `AlwaysAuthGuard`
4. Actualizar `LoginComponent`
5. Actualizar `DefaultLayoutComponent`
6. Actualizar todos los servicios con localStorage
7. Actualizar vistas con localStorage directo

### Fase 3 — Ofuscación
1. Verificar config de producción en `angular.json`
2. Deshabilitar source maps en producción
3. (Opcional) Instalar y configurar javascript-obfuscator

---

## Plan de Verificación

### Manual
- Login → verificar que no hay token en localStorage, sí hay cookie HttpOnly en DevTools.
- Recargar página → el usuario sigue autenticado (cookie persiste).
- Logout → cookie eliminada, redirige a login.
- Intentar acceder a ruta protegida sin cookie → redirige a login.
- Verificar en DevTools → Network que todas las peticiones llevan `withCredentials`.

### Producción
- Build de Angular → verificar que `dist/` no tiene source maps.
- Verificar que el JS está minificado y los nombres ofuscados.
