## Territory Services API

Territory Services API is a RESTful api developed to provide easy and accessible storage and management for congregation territories. Although many systems can connect to the api via mobile and web client, the api does not provide a client interface.

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
 

## Official Documentation

## Contributing

## Security Vulnerabilities

If you discover a security vulnerability within Territory Services API, please send an e-mail to us.

### License

Territory Services API is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
