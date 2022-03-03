# Zephyr

## Introduction

Zephyr provides a minimal and simple starting point for building a Laravel application with authentication and user profile management. 

Zephyr is powered by Blade, Tailwind, Alpine and Livewire.

## Installation

First, you should [create a new Laravel application](https://laravel.com/docs/9.x/installation), configure your database, and run your database migrations:

```bash
curl -s https://laravel.build/example-app | bash

cd example-app

php artisan migrate
```

Once you have created a new Laravel application, you may install Zephyr using Composer:

```bash
composer require fabpl/zephyr --dev
```

After Composer has installed the Zephyr package, you may run the `zephyr:install` Artisan command. 
This command publishes the authentication views, routes, controllers, and other resources to your application. 
After Zephyr is installed, you should also compile your assets so that your application's CSS file is available:

```bash
php artisan zephyr:install

npm install
npm run dev
php artisan migrate
```

Next, you may navigate to your application's `/login` or `/register` URLs in your web browser. 

## License

Zephyr is open-sourced software licensed under the [MIT license](LICENSE.md).
