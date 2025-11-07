# Cafetería Admin - Sistema de Gestión

Sistema web de administración para cafetería desarrollado en PHP, MySQL y JavaScript.

## 📋 Características

- ✅ Sistema de autenticación multi-rol (Administrador, Empleado, Cliente)
- ✅ Gestión de usuarios con roles y permisos
- ✅ Sistema de pedidos y carrito de compras
- ✅ Administración de productos y proveedores
- ✅ Panel de reportes y estadísticas
- ✅ Interfaz responsive y moderna
- ✅ Seguridad con sesiones y prepared statements

## 🏗️ Estructura del Proyecto

```
Web-Admin/
├── config/              # Configuración del sistema
│   ├── config.php       # Configuración general y constantes
│   └── database.php     # Conexión a la base de datos
├── includes/            # Archivos compartidos
│   └── session.php      # Gestión de sesiones
├── public/              # Directorio público (Document Root)
│   ├── index.php        # Página de inicio
│   ├── .htaccess        # Configuración Apache
│   ├── assets/          # Recursos estáticos
│   │   ├── css/         # Hojas de estilo
│   │   ├── js/          # Scripts JavaScript
│   │   └── images/      # Imágenes e iconos
│   └── templates/       # Plantillas de vistas
│       ├── login.php    # Página de login
│       ├── logout.php   # Cerrar sesión
│       ├── carta.php    # Menú público
│       └── admin/       # Área administrativa
├── src/                 # Código fuente
│   ├── auth/            # Autenticación
│   └── controllers/     # Controladores
├── .env.example         # Ejemplo de variables de entorno
└── README.md            # Este archivo
```

## 🚀 Instalación

### Requisitos Previos

- XAMPP (o similar) con:
  - PHP 7.4 o superior
  - MySQL 5.7 o superior
  - Apache con mod_rewrite habilitado
- Navegador web moderno

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   cd C:\xampp\htdocs\tareas-con-xampp\
   git clone https://github.com/WhiteMooncy/Web-Admin.git
   ```

2. **Configurar la base de datos**
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Crear una nueva base de datos llamada `web-admin`
   - Importar el archivo SQL (si existe) o crear las tablas necesarias

3. **Configurar variables de entorno** (opcional)
   ```bash
   cp .env.example .env
   # Editar .env con tus credenciales
   ```

4. **Configurar Apache**
   - El proyecto está configurado para funcionar en: `http://localhost/tareas-con-xampp/Web-Admin/public/`
   - Si usas otro directorio, ajusta `$projectFolder` en `config/config.php`

5. **Verificar permisos**
   - Asegurar que Apache tenga permisos de lectura en todos los directorios

## 🎯 Uso

### Acceder al Sistema

1. Iniciar XAMPP (Apache + MySQL)
2. Abrir navegador en: `http://localhost/tareas-con-xampp/Web-Admin/public/`
3. Para acceder al panel administrativo:
   - URL: `http://localhost/tareas-con-xampp/Web-Admin/public/templates/login.php`
   - Crear un usuario desde el registro o usar credenciales existentes

### Roles y Permisos

- **Administrador**: Acceso completo al sistema
  - Gestión de usuarios
  - Gestión de productos y proveedores
  - Visualización de reportes y estadísticas
  - Gestión de pedidos

- **Empleado**: Acceso limitado
  - Gestión de productos y proveedores
  - Gestión de pedidos
  - Visualización de reportes

- **Cliente**: Acceso básico
  - Realizar pedidos
  - Ver historial de pedidos
  - Actualizar perfil

## 🔧 Configuración

### Editar Configuración General

Archivo: `config/config.php`

```php
// Ajustar según tu instalación
$projectFolder = '/tareas-con-xampp/Web-Admin/public';
```

### Configurar Base de Datos

Archivo: `config/database.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'web-admin');
```

O usar variables de entorno en `.env`:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=web-admin
```

## 📊 Base de Datos

### Tablas Principales

- `usuarios` - Información de usuarios del sistema
- `roles` - Definición de roles (Administrador, Empleado, Cliente)
- `productos` - Catálogo de productos
- `pedidos` - Registro de pedidos
- `proveedores` - Información de proveedores

## 🛡️ Seguridad

- ✅ Contraseñas hasheadas con `password_hash()` (bcrypt)
- ✅ Prepared statements para prevenir SQL injection
- ✅ Validación de sesiones
- ✅ Protección contra XSS con `htmlspecialchars()`
- ✅ Headers de seguridad configurados en `.htaccess`
- ✅ Archivos sensibles protegidos

## 🐛 Solución de Problemas

### Error: "Conexión a base de datos fallida"
- Verificar que MySQL esté corriendo en XAMPP
- Comprobar credenciales en `config/database.php`
- Verificar que la base de datos exista

### Error 404 en assets
- Verificar que las rutas en `config/config.php` sean correctas
- Comprobar que `mod_rewrite` esté habilitado en Apache

### Sesiones no funcionan
- Verificar permisos en el directorio de sesiones de PHP
- Comprobar que las cookies estén habilitadas en el navegador

## 👤 Autor

**WhiteMooncy**
- GitHub: [@WhiteMooncy](https://github.com/WhiteMooncy)

## 🔄 Versión

**v2.0.0** - Reorganización completa del proyecto con arquitectura mejorada
