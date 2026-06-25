# Instalación de MAXIERI RIFAS

## Requisitos

- PHP 8.1 o superior con PDO MySQL.
- MySQL 5.7+ o MariaDB compatible.
- Hosting con soporte para `.htaccess` si deseas URLs como `/rifa/12/numero/37`.

## Pasos

1. Sube todos los archivos al hosting.
2. Crea una base de datos MySQL.
3. Si administras el servidor MySQL, ejecuta primero `database/create_user.sql` con un usuario root o administrador.
4. Importa `database/schema.sql` dentro de la base `maxieri_rifas`.
5. Configura variables de entorno o edita `config/config.php` con los datos reales:
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
6. Entra a `/admin/login.php`.

## Prueba local

Ejecuta:

```bash
php -S 127.0.0.1:8080 router.php
```

Luego abre `http://127.0.0.1:8080`.

## Credenciales de base de datos locales incluidas

- Base de datos: `maxieri_rifas`
- Usuario MySQL: `maxieri_user`
- Contraseña MySQL: `MaxieriRifas2026!`

Estas credenciales son solo para desarrollo local. En produccion usa variables de entorno.

## Acceso inicial

- Usuario: `admin`
- Contraseña: `admin123`

Cambia esta contraseña después del primer ingreso desde `Administración > Contraseña`.

## Estructura

- `admin/`: panel protegido para rifas, números, reportes, exportaciones y sorteos.
- `api/`: endpoints JSON para integraciones ligeras.
- `assets/`: CSS, JavaScript, imágenes y audio.
- `components/`: cabeceras y pies reutilizables.
- `config/`: constantes de aplicación y credenciales de base de datos.
- `database/`: esquema SQL instalable.
- `includes/`: conexión, seguridad, autenticación y funciones del dominio.
- `public/`: vistas públicas de rifas, buscador y números verificables.
- `tickets/`: boletos imprimibles para impresoras térmicas de 58 mm.
- `uploads/`: carpeta reservada para archivos subidos.
