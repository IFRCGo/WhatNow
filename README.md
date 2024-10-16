# Laravel-Vue-Bootstrap Project

A starter web application using **Laravel**, **Vue.js**, and **BootstrapVue**.

## Features
- **Backend**: Laravel API and web routes.
- **Frontend**: Vue.js components.
- **UI Framework**: BootstrapVue for responsive design.
- **SPA**: Single Page Application architecture.

## Requirements
- PHP >= 8.0
- Composer >= 2.x
- Node.js >= 14.x
- MySQL (or supported database)

## Installation
1. **Clone the project**:
   ```bash
   git clone https://github.com/yourusername/your-project.git
   cd your-project
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations**:
   ```bash
   php artisan migrate
   ```

5. **Run the app**:
   ```bash
   php artisan serve
   npm run dev
   ```

## Commands
- Start Laravel server: `php artisan serve`
- Development build: `npm run dev`
- Production build: `npm run build`

## License
This project is licensed under the MIT License.

## Links
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org)
- [BootstrapVue Docs](https://bootstrap-vue.org/docs)
