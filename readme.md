## Territory Services API

Territory Services API is a RESTful api developed to provide easy storage and management for congregation territories. Although clients can connect to the api via mobile and web browser, the api does contain a user interface.

## Core Services

- Authentication
- User Registration
- User Roles and Privileges
- Data Storage and CRUD

## Core Entities

- publishers: name, type (pioneer, regular)
  Crud

- users: email, publisher, type, date
  Crud

- territories: number, location, boundaries (4 points)
  Relationships with publisher
  Crud

- addresses: name, address, status
  Relationships with territories
  Crud

- notes: content, user, type
  Relationships with addresses, territories, and publishers
  Crud

  - territory records: territory, publisher, activity type, date
    Crud

## Additional Services

- Generate PDF
- Address coordinates
- Map image

## Installation

- Step 1 - Clone this repository into your dev directory
- Step 2 - Run `composer install` command
- Step 3 - Then run the following Artisan commands to setup laravel:

> - Create application environment config file, ".env": `php -r "copy('.env.example', '.env');"`

> - Generate application key: `php artisan key:generate`

> - Optimize application: `php artisan clear-compiled` `php artisan optimize`

- Step 4 - Open the file, `.env` and add the required configurations (a MySQL database and Google Maps Api key with no HTTP restrictions needed).

> - Run migration code: `php artisan migrate` This will install an admin user with the credentials stored in your .env file for "APP_ADMIN_EMAIL" and "APP_ADMIN_PASSWORD".

## Official Documentation

[https://wwww.territory-app.net/](https://wwww.territory-app.net/)

## Contributing

To be part of this project, send email to territoryapi@gmail.com

## Security Vulnerabilities

If you discover a security vulnerability within Territory Services API, please send an e-mail to us.

### License

Territory Services API is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
