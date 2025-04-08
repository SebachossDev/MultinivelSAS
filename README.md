# MultinivelSAS

This project is developed in Laravel and uses Filament as an administration tool. Below are the steps required to set up, start, and maintain the project.

## Prerequisites

- PHP >= 8.3
- Composer
- Node.js and npm
- MySQL or any database compatible with Laravel
- PHP extensions required by Laravel

## Installation

1. Install PHP dependencies:
    ```bash
    composer install
    ```

2. Install Node.js dependencies:
    ```bash
    npm install
    ```

3. Copy the example configuration file and configure it:
    ```bash
    cp .env.example .env
    ```
    Edit the `.env` file to configure the database connection and other necessary variables.

4. Generate the application key:
    ```bash
    php artisan key:generate
    ```

## Migrations and Seeders

1. Run migrations to create the database tables:
    ```bash
    php artisan migrate
    ```

2. If the project includes seeders, run them to populate the database with initial data:
    ```bash
    php artisan db:seed
    ```

## Asset Compilation

1. Compile the frontend assets:
    ```bash
    npm run dev
    ```
    For production:
    ```bash
    npm run build
    ```

## Using Filament

1. Access the Filament admin panel at:
    ```
    http://<YOUR_DOMAIN>/dashboard
    ```

2. If you need to create an admin user, use the following command:
    ```bash
    php artisan make:filament-user
    ```

## Updates

1. Update Composer dependencies:
    ```bash
    composer update
    ```

2. Update npm dependencies:
    ```bash
    npm update
    ```

3. If there are new migrations, run them:
    ```bash
    php artisan migrate
    ```

4. If there are new seeders, run them:
    ```bash
    php artisan db:seed
    ```

## Useful Commands and Starting the Application

- Clear configuration caches:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear
  ```

- Check the status of migrations:
  ```bash
  php artisan migrate:status
  ```

- Start the server:
    ```bash
    php artisan serve
    ```

- Run the worker for sending password recovery emails:
    ```bash
    php artisan queue:work
    ```

## Notes

- Ensure the web server (Apache, Nginx, etc.) is properly configured to start the project.
- Set the appropriate permissions for the `storage` and `bootstrap/cache` folders.
- For sending password recovery emails, customize the `.env` file according to the email-sending application you use. In this case, mailtrap.io is used, but it may differ for your setup.
- The database name in the `.env` file is fully customizable and does not necessarily need to match the example.

That's it! You should now have the project running correctly.
