# Guía de Migración - Web-Admin v1.0 → v2.0

## 📌 Resumen de Cambios

El proyecto ha sido completamente reorganizado para seguir una arquitectura más profesional y mantenible.

### Cambios Principales

1. **Nueva estructura de directorios** - Separación clara entre configuración, código fuente y recursos públicos
2. **Sistema de configuración centralizado** - Todas las constantes y rutas en un solo lugar
3. **Gestión de sesiones mejorada** - Funciones helper para autenticación y autorización
4. **Rutas relativas consistentes** - Uso de constantes PHP en lugar de rutas hardcodeadas
5. **Seguridad mejorada** - Headers, validaciones y protección de archivos sensibles

---

## 🗂️ Mapeo de Directorios

### Estructura Antigua → Nueva

```
ANTIGUA ESTRUCTURA                    NUEVA ESTRUCTURA
==================                    ================

assets/                              public/assets/
├── css/                       →     ├── css/
├── js/                        →     ├── js/
├── php/                       →     (movido a src/ y config/)
│   ├── conexion/              →     config/ y includes/
│   └── funcions/              →     src/controllers/
└── sources/                   →     public/assets/images/

templates/                           public/templates/
├── carta.php                  →     carta.php
├── notfi.html                 →     notfi.html
└── loged/                     →     admin/
    ├── dashboard.php          →     dashboard.php
    ├── form_login.php         →     ../login.php
    └── [otros archivos]       →     [mismo nombre]

index.php                      →     public/index.php
```

---

## 🔄 Cambios en Rutas

### Archivos de Configuración

**ANTES:**
```php
require_once '../../../backend/php/conexion/db.php';
require_once '../../../backend/php/conexion/check_role.php';
```

**AHORA:**
```php
define('APP_ROOT', dirname(__DIR__, 2));  // Ajustar según ubicación
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';
```

### Enlaces a Assets (CSS, JS, Imágenes)

**ANTES:**
```html
<link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
<img src="../src/menu/cafe.jpeg" alt="Café">
```

**AHORA:**
```php
<link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styleDashboard.css?v=3">
<img src="<?php echo ASSETS_URL; ?>/images/menu/Capuccino.jpeg" alt="Capuccino">
```

### Enlaces entre Páginas

**ANTES:**
```html
<a href="../loged/dashboard.php">Dashboard</a>
<a href="../../../backend/php/conexion/logout.php">Cerrar Sesión</a>
```

**AHORA:**
```php
<a href="<?php echo BASE_URL; ?>/templates/admin/dashboard.php">Dashboard</a>
<a href="<?php echo BASE_URL; ?>/templates/logout.php">Cerrar Sesión</a>
```

---

## 📝 Nuevas Constantes Disponibles

Definidas en `config/config.php`:

| Constante | Descripción | Ejemplo |
|-----------|-------------|---------|
| `APP_ROOT` | Raíz del proyecto | `C:\xampp\htdocs\...\Web-Admin` |
| `CONFIG_PATH` | Directorio de configuración | `APP_ROOT/config` |
| `INCLUDES_PATH` | Directorio de includes | `APP_ROOT/includes` |
| `SRC_PATH` | Código fuente | `APP_ROOT/src` |
| `PUBLIC_PATH` | Directorio público | `APP_ROOT/public` |
| `ASSETS_PATH` | Assets en servidor | `PUBLIC_PATH/assets` |
| `TEMPLATES_PATH` | Templates en servidor | `PUBLIC_PATH/templates` |
| `BASE_URL` | URL base del proyecto | `http://localhost/.../public` |
| `ASSETS_URL` | URL de assets | `BASE_URL/assets` |

---

## 🔐 Nuevas Funciones de Sesión

Definidas en `includes/session.php`:

| Función | Descripción | Uso |
|---------|-------------|-----|
| `startSecureSession()` | Inicia sesión segura | Automático al incluir session.php |
| `isAuthenticated()` | Verifica si hay sesión activa | `if (isAuthenticated()) { ... }` |
| `requireAuth($redirect)` | Requiere autenticación o redirige | `requireAuth();` |
| `hasRole($roleName)` | Verifica rol del usuario | `if (hasRole('administrador')) { ... }` |
| `requireRole($roleName)` | Requiere rol específico | `requireRole('administrador');` |
| `destroySession()` | Cierra sesión completamente | `destroySession();` |
| `getCurrentUser()` | Obtiene datos del usuario | `$user = getCurrentUser();` |

---

## 📋 Lista de Verificación para Migrar Archivos

Cuando migres un archivo existente, sigue estos pasos:

### 1. Actualizar Encabezado PHP

```php
<?php
// Definir la raíz del proyecto (ajustar según ubicación del archivo)
define('APP_ROOT', dirname(__DIR__, 2));

// Cargar configuración
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';

// Verificar autenticación si es necesario
requireAuth();
// requireRole('administrador'); // Si requiere rol específico
?>
```

**Notas:**
- `dirname(__DIR__, 2)` sube 2 niveles desde el archivo actual
- Ajustar según la ubicación: archivos en `public/` = 1 nivel, en `public/templates/` = 2 niveles, etc.

### 2. Reemplazar Rutas en HTML

Buscar y reemplazar:

```php
// Enlaces
href="../templates/carta.php"           →  href="<?php echo BASE_URL; ?>/templates/carta.php"
href="../loged/dashboard.php"           →  href="<?php echo BASE_URL; ?>/templates/admin/dashboard.php"

// CSS
href="../../src/css/style.css"          →  href="<?php echo ASSETS_URL; ?>/css/style.css"

// JavaScript
src="../../src/js/script.js"            →  src="<?php echo ASSETS_URL; ?>/js/script.js"

// Imágenes
src="../src/menu/cafe.jpeg"             →  src="<?php echo ASSETS_URL; ?>/images/menu/cafe.jpeg"
src="../../frontend/src/icons/logo.png" →  src="<?php echo ASSETS_URL; ?>/images/icons/logo.png"
```

### 3. Actualizar Formularios

```php
// ANTES
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

// AHORA (si se procesa en la misma página)
<form action="" method="post">

// O especificar ruta completa
<form action="<?php echo BASE_URL; ?>/templates/admin/process_form.php" method="post">
```

### 4. Reemplazar Variables de Sesión

```php
// ANTES
if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: ../templates/loged/form_login.php');
    exit();
}

// AHORA
requireAuth();  // Automáticamente verifica y redirige
```

### 5. Verificar Consultas a Base de Datos

```php
// ANTES
require_once '../../../backend/php/conexion/db.php';
// $conn ya está disponible

// AHORA
require_once CONFIG_PATH . '/database.php';
// $conn ya está disponible (igual que antes)
```

---

## 🧪 Pruebas Necesarias

Después de migrar, probar:

### 1. Páginas Públicas
- [ ] `http://localhost/tareas-con-xampp/Web-Admin/public/` - Página de inicio
- [ ] `http://localhost/tareas-con-xampp/Web-Admin/public/templates/login.php` - Login
- [ ] `http://localhost/tareas-con-xampp/Web-Admin/public/templates/carta.php` - Menú

### 2. Autenticación
- [ ] Registro de nuevo usuario
- [ ] Login con credenciales correctas
- [ ] Login con credenciales incorrectas
- [ ] Logout

### 3. Panel Administrativo
- [ ] Dashboard principal
- [ ] Gestión de usuarios (si eres admin)
- [ ] Gestión de productos
- [ ] Visualización de pedidos
- [ ] Reportes

### 4. Recursos Estáticos
- [ ] CSS carga correctamente
- [ ] JavaScript funciona
- [ ] Imágenes se muestran
- [ ] Iconos visibles

---

## ⚠️ Problemas Comunes y Soluciones

### Error: "Cannot find config.php"

**Causa:** Ruta incorrecta a `APP_ROOT`

**Solución:**
```php
// Verificar cuántos niveles subir
// Desde public/index.php
define('APP_ROOT', __DIR__);

// Desde public/templates/login.php
define('APP_ROOT', dirname(__DIR__));

// Desde public/templates/admin/dashboard.php
define('APP_ROOT', dirname(__DIR__, 2));
```

### Error: "Undefined constant BASE_URL"

**Causa:** No se cargó `config/config.php`

**Solución:** Asegurar que estas líneas estén al inicio:
```php
define('APP_ROOT', dirname(__DIR__, X));
require_once APP_ROOT . '/config/config.php';
```

### Imágenes no cargan (404)

**Causa:** Rutas incorrectas o archivos no copiados

**Solución:**
1. Verificar que las imágenes estén en `public/assets/images/`
2. Usar `<?php echo ASSETS_URL; ?>/images/ruta/archivo.jpg`
3. Verificar permisos de lectura en los archivos

### CSS no aplica

**Causa:** Ruta incorrecta o caché del navegador

**Solución:**
1. Verificar ruta: `<?php echo ASSETS_URL; ?>/css/styleDashboard.css`
2. Incrementar versión: `?v=3` al final de la URL
3. Limpiar caché del navegador (Ctrl+F5)

---

## 📦 Archivos que Deben Moverse/Copiarse

### Copiar CSS
```powershell
Copy-Item 'assets\css\*' -Destination 'public\assets\css\' -Recurse -Force
```

### Copiar JavaScript
```powershell
Copy-Item 'assets\js\*' -Destination 'public\assets\js\' -Recurse -Force
```

### Copiar Imágenes
```powershell
Copy-Item 'assets\sources\*' -Destination 'public\assets\images\' -Recurse -Force
```

---

## ✅ Validación Final

Antes de considerar la migración completa:

1. [ ] Todas las páginas cargan sin errores 500/404
2. [ ] Login y logout funcionan correctamente
3. [ ] Permisos por rol se respetan
4. [ ] Assets (CSS/JS/Imágenes) cargan correctamente
5. [ ] Formularios se envían correctamente
6. [ ] Base de datos se conecta sin errores
7. [ ] No hay errores en consola del navegador
8. [ ] No hay errores en logs de PHP (error.log de Apache)

---

## 📚 Referencias

- Documentación completa: `README.md`
- Configuración: `config/config.php`
- Funciones de sesión: `includes/session.php`
- Autenticación: `src/auth/auth.php`

---

**Última actualización:** 7 de noviembre de 2025  
**Versión:** 2.0.0
