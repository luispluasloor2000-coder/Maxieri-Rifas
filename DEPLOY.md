# Despliegue de Maxieri en Railway

Railway es la opcion recomendada para este proyecto porque permite crear una base MySQL desde el panel, configurar variables de entorno y desplegar el contenedor Docker del proyecto sin convertirlo a un framework.

## 1. Crear la cuenta

1. Entra a `https://railway.com`.
2. Crea una cuenta o inicia sesion.
3. Conecta tu cuenta de GitHub si quieres despliegues automaticos.

## 2. Subir el proyecto

1. Crea un repositorio en GitHub.
2. Sube el contenido completo del proyecto.
3. En Railway selecciona `New Project`.
4. Elige `Deploy from GitHub repo`.
5. Selecciona el repositorio de Maxieri.

Railway detectara el `Dockerfile` y construira la aplicacion como contenedor.

## 3. Configurar la base de datos

1. Dentro del proyecto de Railway pulsa `New`.
2. Selecciona `Database`.
3. Elige `MySQL`.
4. Espera a que Railway cree el servicio.
5. Abre la pestana `Variables` o `Connect` del servicio MySQL para copiar host, puerto, usuario, contrasena y nombre de base de datos.

## 4. Importar `database/schema.sql`

Desde tu computadora puedes importar el esquema con un cliente MySQL:

```bash
mysql -h TU_HOST -P TU_PUERTO -u TU_USUARIO -p TU_BASE < database/schema.sql
```

Railway suele usar una base llamada `railway`. El archivo `schema.sql` esta preparado para crear las tablas sobre la base que ya seleccionaste en la conexion.

## 5. Configurar variables de entorno

En el servicio web de Maxieri, abre `Variables` y agrega:

```env
APP_NAME=MAXIERI RIFAS
APP_SLOGAN=Cada numero tiene una historia.
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-url-de-railway.up.railway.app
APP_TIMEZONE=America/Guayaquil
DB_HOST=host_mysql_de_railway
DB_PORT=3306
DB_NAME=railway
DB_USER=root
DB_PASS=contrasena_mysql_de_railway
DB_CHARSET=utf8mb4
```

Usa los valores reales del servicio MySQL de Railway.

## 6. Primer despliegue

1. Abre el servicio web.
2. En `Settings` genera un dominio publico.
3. Copia ese dominio en `APP_URL`.
4. Ejecuta un redeploy.
5. Abre `/admin/login.php`.
6. Ingresa con:

```text
Usuario: admin
Contrasena: admin123
```

7. Cambia la contrasena inmediatamente desde `Contraseña`.

## 7. Actualizar el proyecto

1. Realiza cambios localmente.
2. Sube los cambios a GitHub.
3. Railway desplegara automaticamente si el deploy automatico esta activo.
4. Si cambias variables de entorno o credenciales de la base, ejecuta `Redeploy`.

## Notas de produccion

- No uses credenciales fijas en archivos PHP.
- Mantén `APP_DEBUG=false`.
- Haz respaldos periodicos de la base.
- No publiques `database/create_user.sql` con credenciales reales.
- Para un uso comercial o con ventas reales, considera un plan pagado y monitoreo de disponibilidad.
