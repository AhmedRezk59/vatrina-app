## E-commerce Application

This is an E-commerce application made with Laravel, PHPUnit and MySQL.

### Features

- Multible Authentication system for admins,vendors and users using JWT.
- Service to send messages using Whatsapp.
- Collections for products.
- Products
- Cart system
- Orders
- Permissions utility for the admins.
- Ability to ban vendors.
- live chat between vendors and users using pusher.
- Unit tests for all the features to provide confidence and ease in extending the project.

### Installation
##### You must have composer and php 8 up and running on your PC.

- Open the terminal in your project folder.
- Run `composer install`
- Run `cp .env.example .env`
- Run `php artisan key:generate`
- Configure your database configurations in `.env` file.
- Run `php artisan migrate --seed`
- Run `php artisan jwt:secret`
- Run `php artisan serve`

## Have fun!
