## Territory Services API

Territory Services API is a RESTful api developed to provide easy and accessible storage and management for congregation territories. Although many systems can connect to the api via mobile and web client, the api does contain a client interface.

## Core Services

- Authentication
- User Registration 
- User Roles and Privileges 
- Data Storage and CRUD

## Core Entities 

- publishers: name, type (pioneer, regular)
   Crud 

- territory records: territory, publisher, activity type, date
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

## Additional Services 

- pdf 
   Generate PDF
  
- map
   Address coordinates
   Map image
 

## Installation

- Step 1 - Clone this repository into you dev directory
- Step 2 - Run `composer update --no-scripts` command to download vendor libraries
- Step 3 - Then run the following Artisan commands to setup laravel:

 - Create application environment config file, ".env": `php -r "copy('.env.example', '.env');"` 

 - Generate application key: `php artisan key:generate` 

 - Optimize application: `php artisan clear-compiled`  `php artisan optimize` 

 - Run migration code: `php artisan migrate` This will install an admin user with the credentials stored in your .env file for "APP_ADMIN_EMAIL" and "APP_ADMIN_PASSWORD". 

- Step 4 - Open the file .env and setup your database configuration and mail. Other configurations are optional.


## Official Documentation

Coming soon.

## Contributing

To be part of this project, send email to territoryapi@gmail.com

## Security Vulnerabilities

If you discover a security vulnerability within Territory Services API, please send an e-mail to us.

### License

Territory Services API is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
