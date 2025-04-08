# MultinivelSAS

Este proyecto está desarrollado en Laravel y utiliza Filament como herramienta de administración. A continuación, se describen los pasos necesarios para configurar, iniciar y mantener el proyecto.

## Requisitos previos

- PHP >= 8.3
- Composer
- Node.js y npm
- MySQL o cualquier base de datos compatible con Laravel
- Extensiones de PHP requeridas por Laravel

## Instalación



1. Instala las dependencias de PHP:
    ```bash
    composer install
    ```

2. Instala las dependencias de Node.js:
    ```bash
    npm install
    ```

3. Copia el archivo de configuración de ejemplo y configúralo:
    ```bash
    cp .env.example .env
    ```
    Edita el archivo `.env` para configurar la conexión a la base de datos y otras variables necesarias.

4. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```

## Migraciones y Seeders

1. Ejecuta las migraciones para crear las tablas en la base de datos:
    ```bash
    php artisan migrate
    ```

2. Si el proyecto incluye seeders, ejecútalos para poblar la base de datos con datos iniciales:
    ```bash
    php artisan db:seed
    ```

## Compilación de Assets

1. Compila los assets del frontend:
    ```bash
    npm run dev
    ```
    Para producción:
    ```bash
    npm run build
    ```

## Uso de Filament

1. Accede al panel de administración de Filament en:
    ```
    http://<TU_DOMINIO>/dashboard
    ```

2. Si necesitas crear un usuario administrador, utiliza el siguiente comando:
    ```bash
    php artisan make:filament-user
    ```

## Actualizaciones

1. Actualiza las dependencias de Composer:
    ```bash
    composer update
    ```

2. Actualiza las dependencias de npm:
    ```bash
    npm update
    ```

3. Si hay nuevas migraciones, ejecútalas:
    ```bash
    php artisan migrate
    ```

4. Si hay nuevos seeders, ejecútalos:
    ```bash
    php artisan db:seed
    ```

## Comandos Útiles y para iniciar la aplicacion

- Limpiar cachés de configuración:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear
  ```

- Verificar el estado de las migraciones:
  ```bash
  php artisan migrate:status
  ```

- Iniciar el servidor
    ´´´bash
    php artisan serve
    ´´´
- Ejecutar work para el envio del correo de recuperación
    ´´´bash
        php artisan queue:work
    ´´´    
## Notas

- Asegúrate de que el servidor web (Apache, Nginx, etc.) esté configurado correctamente para iniciar el proyecto.
- Configura los permisos adecuados para las carpetas `storage` y `bootstrap/cache`.
- Para el envio del correo de recuperación se tiene que personalizar el `.env` de acuerdo a la aplicación de envios de correos
    que utilices, en este caso se usa mailtrap.io, en tu caso puede ser diferente.
- También para la base de datos no necesariamente se debe llamar como estara en el `.env`, es totalmente personalizable.

¡Listo! Ahora deberías tener el proyecto funcionando correctamente.  