
# WhatNow

WhatNow is a web application built with Laravel 5.8 and Vue.js, designed to provide a disaster preparedness guide. This document includes steps to install and configure the project in your development environment.

## Prerequisites

Ensure the following tools are installed on your system:

- **PHP** (version 7.4):
  ```bash
  php -v
  ```
- **Composer** (dependency manager for PHP):
  ```bash
  composer --version
  ```
- **Node.js** (recommended version 12 or higher):
  ```bash
  node -v
  ```
- **npm** (Node.js package manager):
  ```bash
  npm -v
  ```
- **MySQL** (version 5.7 or higher):
  ```bash
  mysql --version
  ```

## Project Installation

1. **Clone the Repository**
    ```bash
    git clone https://github.com/YourUsername/WhatNow.git
    cd WhatNow
    ```

2. **Install PHP Dependencies**
    ```bash
    composer install
    ```

3. **Install JavaScript Dependencies**
    ```bash
    npm install
    ```

4. **Set Up Environment**
    - Copy the `.env` example file and configure it as your environment file:
      ```bash
      cp .env.example .env
      ```
    - Generate an application key for Laravel:
      ```bash
      php artisan key:generate
      ```

5. **Database Configuration**
    - Create a MySQL database named `homestead`.
    - Open the `.env` file and update your database credentials:

    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

6. **Run Database Migrations**
    ```bash
    php artisan migrate
    ```

   *Optional:* If you would like to populate the database with initial sample data, you can run the database seeder command:
    ```bash
    php artisan db:seed
    ```

7. **Compile Frontend Assets**
    - For development:
      ```bash
      npm run hot
      ```
    - For production:
      ```bash
      npm run production
      ```

8. **Start the Development Server**
    ```bash
    php artisan serve
    ```

## Development Commands

| Command                      | Description                                      |
|------------------------------|--------------------------------------------------|
| `php artisan serve`          | Starts the local development server              |
| `npm run hot`                | Compiles assets and hot-reloads changes          |
| `npm run production`         | Compiles assets for production                   |
| `php artisan migrate`        | Runs database migrations                         |
| `php artisan db:seed`        | Seeds the database with sample data              |
| `php artisan make:model`     | Creates a new Eloquent model                     |
| `php artisan make:controller`| Creates a new controller                         |

## Documentation

- [Laravel 5.8 Documentation](https://laravel.com/docs/5.8)
- [Vue.js Documentation](https://vuejs.org/v2/guide/)
- [Bootstrap 4 Documentation](https://getbootstrap.com/docs/4.3/getting-started/introduction/)
- [BootstrapVue Docs](https://bootstrap-vue.org/docs)

## Troubleshooting

1. **Verify Dependencies:** Ensure all dependencies are properly installed.
2. **Environment File:** Check that the `.env` file is correctly set up.
3. **Dependency Conflicts:** If issues arise, run `composer update` and `npm install` to resolve them.
4. **Database Connection:** Verify that the database credentials in the `.env` file are correct.
5. **Migrations:** If migrations fail, check the database connection and run `php artisan migrate:refresh`.
6. **Frontend Assets:** If assets are not compiling, run `npm run production` or `npm run hot` to recompile them.
7. **Development Server:** If the server fails to start, check the port and run `php artisan serve` again.

## Testing

Run tests using `phpunit`. *(Note: Tests may require review and adjustments to function properly.)*
```bash
./vendor/bin/phpunit
```
