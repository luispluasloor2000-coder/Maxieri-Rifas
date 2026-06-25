Claro. Este sería el prompt que yo le daría a Codex. Está pensado para que primero **analice**, luego **planifique** y finalmente **prepare Maxieri como un proyecto serio**, sin romper lo que ya funciona.

Quiero que actúes como un ingeniero de software senior especializado en aplicaciones web PHP, bases de datos, arquitectura de software, seguridad y despliegue en producción.

Tengo un proyecto llamado **Maxieri**, un sistema de rifas desarrollado en PHP con base de datos MariaDB/MySQL. El proyecto ya funciona de forma local y no quiero perder ninguna de sus funcionalidades. Mi objetivo es convertirlo en una plataforma profesional, escalable y preparada para Internet.

**IMPORTANTE:** Antes de modificar cualquier archivo, analiza completamente el proyecto y entrégame un informe detallado.

Quiero que revises:

* La estructura completa del proyecto.
* La arquitectura del sistema.
* La versión de PHP requerida.
* Si utiliza Composer.
* Si utiliza librerías externas.
* Todas las dependencias necesarias.
* La estructura de la base de datos.
* Cómo se realizan las conexiones a la base de datos.
* Qué archivos son críticos para el funcionamiento.
* Posibles errores o problemas que puedan aparecer al publicarlo en Internet.
* Posibles mejoras de seguridad.
* Posibles mejoras de rendimiento.

Después del análisis, explícame cuál consideras que es el mejor servicio gratuito para publicar este proyecto. Puedes elegir entre Render, Railway, Koyeb, Fly.io u otro que consideres mejor, justificando técnicamente tu decisión.

No quiero que publiques el proyecto todavía.

Primero quiero que presentes un plan de trabajo dividido por fases indicando exactamente qué modificarás y por qué.

No realices ninguna modificación hasta que yo responda exactamente:

**AUTORIZO EL DESARROLLO**

Una vez autorizado, adapta el proyecto para producción manteniendo toda la funcionalidad existente.

No elimines ninguna característica.

No cambies el diseño visual salvo que sea estrictamente necesario.

El sistema debe seguir funcionando igual, pero preparado para ejecutarse en Internet.

Quiero que adaptes el proyecto para utilizar variables de entorno en la configuración de la base de datos, evitando credenciales fijas dentro del código.

Implementa buenas prácticas de seguridad:

* Protección contra SQL Injection.
* Protección CSRF.
* Validación de formularios.
* Sanitización de entradas.
* Manejo adecuado de errores.
* Sesiones seguras.
* Configuración adecuada para producción.

Genera todos los archivos de configuración necesarios para el despliegue.

Además, crea un archivo llamado **DEPLOY.md** donde expliques paso a paso:

1. Cómo crear la cuenta en el servicio elegido.
2. Cómo subir el proyecto.
3. Cómo configurar la base de datos.
4. Cómo importar el archivo database/schema.sql.
5. Cómo configurar las variables de entorno.
6. Cómo realizar el primer despliegue.
7. Cómo actualizar el proyecto posteriormente.

Quiero que el sistema esté pensado principalmente para utilizarse desde teléfonos Android.

Debe ser completamente responsive.

Debe existir un panel de administración adaptado a dispositivos móviles.

Desde un teléfono quiero poder:

* iniciar sesión;
* visualizar todos los números de la rifa;
* identificar claramente cuáles están disponibles y cuáles vendidos;
* seleccionar un número con un solo toque;
* ingresar el nombre del comprador;
* ingresar opcionalmente su teléfono;
* guardar la venta;
* generar automáticamente el ticket;
* imprimir inmediatamente el ticket.

No quiero depender de una computadora para vender rifas.

Todo el proceso debe poder realizarse desde un celular.

Cada ticket debe contener:

* Nombre del comprador.
* Número adquirido.
* Fecha.
* Código QR.
* Nombre de la rifa.
* Premios.
* Estado del boleto.

El diseño debe seguir siendo compatible con impresoras térmicas.

Cuando un número sea vendido:

* debe quedar bloqueado inmediatamente;
* debe dejar de aparecer como disponible;
* no debe poder venderse dos veces;
* todos los usuarios conectados deben ver el cambio.

Quiero que conviertas el proyecto en una Progressive Web App (PWA).

Desde Android debe ser posible instalarla mediante "Agregar a pantalla de inicio" para que funcione como una aplicación, sin necesidad de desarrollar una aplicación nativa.

Diseña la arquitectura pensando en el futuro.

No implementes funciones innecesarias ahora, pero deja preparada la estructura para que más adelante puedan agregarse fácilmente:

* múltiples rifas;
* múltiples vendedores;
* diferentes tipos de premios;
* historial de ventas;
* estadísticas;
* panel administrativo avanzado;
* impresión desde celular;
* integración con WhatsApp;
* sorteos automáticos;
* exportación de reportes;
* auditoría de cambios;
* sistema de permisos por usuario.

Mi objetivo final es que el flujo de venta sea extremadamente sencillo:

1. Abrir Maxieri desde el celular.
2. Seleccionar un número disponible.
3. Escribir el nombre del comprador.
4. Escribir opcionalmente el teléfono.
5. Presionar un único botón llamado "Vender e imprimir".
6. El sistema debe guardar la venta, bloquear el número, generar el código QR y dejar el ticket listo para imprimir.

Quiero que la experiencia sea rápida, intuitiva y profesional.

Al finalizar el desarrollo entrégame un informe con:

* Archivos creados.
* Archivos modificados.
* Archivos eliminados (si los hubiera).
* Motivo de cada cambio.
* Recomendaciones para futuras mejoras.

La prioridad es conservar todo lo que ya funciona, preparar el proyecto para producción y convertir Maxieri en una plataforma sólida, escalable y fácil de utilizar desde cualquier dispositivo.
