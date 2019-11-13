# Picara PHP Web Development Framework 

A PHP rapid development framework for MVC http/rest/cli applications, developed since 2007. It focuses on convention over configuration and implements implicit routing. It offers a flexible admin site from scratch, rich scaffolding and libraries for cache generation, queries, lang files, pagination, sessions, validation, forms, email, logs, images, file uploads, static pages and more. 

PicaraPHP is fast, self-contained and has no dependencies nor middleware. It can be used to create applications with different access points: http, command-line, RestFUL API and the admin site.  

## Supported Database Systems
- SQLite
- MySql
- MariaDB
- PostgreSQL
- Oracle8

## Requisites

- Apache Server > 2.2 + mod_rewrite
- PHP > 7.0 + pcre + mbstring + curl + gd + Reflection + json + yaml + libxml3 + sqlite3 + pdo_sqlite + session + PDO

## Getting Started

Open a terminal in your apache document root folder and clone the repository.
```
git clone https://github.com/flaab/picara-php-framework.git
```

### Installing

A step by step series of examples that tell you how to get a development env running

Say what the step will be

```
Give the example
```

And repeat

```
until finished
```

End with an example of getting some data out of the system or using it for a little demo

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Deployment

Create and activate an Apache VirtualHost that points to the htdocs/ folder, and you are ready to go.
```
<VirtualHost _default_:80>
        ServerAdmin you@yourdomain.com
        ServerName yourdomain.com
        ServerAlias www.yourdomain.com
        DocumentRoot /home/you/www/picara-php-framework/htdocs/
        <Directory //home/you/www/picara-php-framework/htdocs/>
                Options -Indexes +FollowSymLinks +MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
		          Require all granted
        </Directory>
</VirtualHost>
```

## Built With

* [ADODB](https://github.com/ADOdb/ADOdb) - Database Abstraction Layer 
* [PHPMAILER](https://github.com/PHPMailer/PHPMailer) - PHP Email Client


## Authors

* **Arturo Lopez Perez** - *Initial work* - [PurpleBooth](https://github.com/PurpleBooth)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
