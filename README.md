# Picara PHP Web Development Framework 
A self-contained PHP rapid development framework for MVC http/rest/cli applications, developed since 2007. It focuses on convention over configuration and implements implicit routing. It offers a flexible admin site from scratch, rich scaffolding and libraries for cache generation, queries, lang files, pagination, sessions, validation, forms, email, logs, images, file uploads, static pages and more. 
 

### What makes it different?
Picara is a self-contained framework that can be installed simply by cloning a git repository. It requires no composer dependencies and uses no middleware, and yet, it is just as powerful and fast as other, full-blown and complex alternatives.


### What can it be used for?
It can be used to build anything. Simple relational websites such as as blogs or stores, complex relational RestFul API services or high-traffic relational websites such as newspapers or online services. The framework implements a comprehensive cache system that can serve static files generated from your routes, which allows it to handle tons of requests without raising the database layer or loading the application libraries.


## Main Features
- Strict MVC architecture
- Supports SQLite, MySQL, MariaDB, PostgreSQL and Oci8
- Supports has_one, has_many, belongs_to and has_and_belongs_to_many relationships
- Model relationships are inferred from table names, or declared in the models
- Lang file support for multi-language sites
- Automatic and customizable Admin Site
- Built-in full-text search engine
- Model and Controller callbacks
- Comprehensive scaffolding


### Other features
- You can create HTTP and CLI controllers that access the same resources
- You can create RESTful API controllers and define acceptable request types
- You can create administration tasks which are easily executed from the *Admin Site*
- You can create model actions which are easily executed from the *Admin Site*
- Implicit routing: there is no need to define routes for each method
- Explicit routing is allowed if you wish to define your own routes
- It can handle multiple and different database connections
- HTTP Controllers have built-in session and IP controls
- Create as many logs as you need in your application

## Requirements
- Apache Server > 2.2
- PHP > 7.0 

### Apache Modules required
- mod_rewrite

### PHP Modules required
- pcre
- mbstring
- cur
- gd
- Reflection
- json
- yaml
- libxml3
- sqlite3
- pdo_sqlite
- session
- PDO


## Installation
No composer or package manager needed. Follow these simple steps.

1. Open a terminal
2. Go to your Apache DocumentRoot Directory
3. Execute the following command to clone the repository
```
git clone https://github.com/flaab/picara-php-framework.git
```
4. Point your browser to http://localhost/picara-php-framework/htdocs.
5. Install any missing libraries if prompted

That's it. You are ready to start coding.


## The command line scripts
The framework ships with command line scripts to perform the following tasks.

- Create or delete logs
```
php scripts/picara create log logname
php scripts/picara destroy log logname
```
- Create or delete models
```
php scripts/picara create model modelname
php scripts/picara create model modelname --table="table_name" --display="Users"
php scripts/picara destroy model modelname
```
- Create or delete http controllers
```
php scripts/picara create controller controllername
php scripts/picara destroy controller controllername
```
- Create or delete cli controllers
```
php scripts/picara create shell controllername
php scripts/picara destroy shell controllername
```
- Create admin controllers
```
php scripts/picara create admincontroller controllername
```
- Create, test or destroy database connections
```
php scripts/picara create connection main
php scripts/picara create connection main -adapter=mysql -host=localhost -db=db_name -user=my_user -password=mypassword
php scripts/picara test connectin main
php scripts/picara destroy connection main
```
- Get or change the execution environment
```
php scripts/picara environment
php scripts/picara environment change (production|development|testing)
```
- List one or all the application components
```
php scripts/picara list all
php scripts/picara list models
php scripts/picara list controllers
php scripts/picara list shells
php scripts/picara list connections
php scripts/picara list logs
```
- Scaffold one or all models
```
php scripts/picara scaffold model modelname
php scripts/picara scaffold all
```

## Start your development
Develop your application in the testing environment and when you are finished, go to the next step.


## Deployment
### 1. Create a VirtualHost in your production server
Create and activate an Apache VirtualHost that points to the htdocs/ folder in your server.
```
<VirtualHost _default_:80>
        ServerAdmin you@yourdomain.com
        ServerName yourdomain.com
        ServerAlias www.yourdomain.com
        DocumentRoot /home/you/www/picara-php-framework/htdocs/
        <Directory /home/you/www/picara-php-framework/htdocs/>
                Options -Indexes +FollowSymLinks +MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
		Require all granted
        </Directory>
	ErrorLog /home/you/path/to/error.log
</VirtualHost>
```
To serve the page via HTTPS, define your VirtualHosts as follows instead.
```
<VirtualHost _default_:443>
        ServerAdmin you@yourdomain.com
        ServerName yourdomain.com
        ServerAlias www.yourdomain.com
        DocumentRoot /home/you/www/picara-php-framework/htdocs/

        SSLEngine on
        SSLProtocol -all +TLSv1.2        
        SSLCertificateKeyFile   /home/you/path/to/yourdomain_com.key
        SSLCertificateFile      /home/you/path/to/yourdomain_com.crt
        SSLCertificateChainFile /home/you/path/to/yourdomain_com.ca-bundle

        <Directory /home/you/www/picara-php-framework/htdocs/>
                Options -Indexes +FollowSymLinks +MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
		Require all granted
        </Directory>
        ErrorLog /home/you/path/to/error.log
</VirtualHost>

```
### 2. Secure your deployment
- Delete the phpinfo.php file located at htdocs/webroot/phpinfo.php
- Delete or rename the PhpLiteAdmin folder located at htdocs/webroot/phpliteadmin
- Add a route to hide/rename your Admin Site URL at app/config/routes.yml

### 3. Test your deployment
Switch to the development environment to test your application in the new server.
```
php scripts/picara environment change development
```
### 4. Go public
Finally, switch to the production environment before going public.
```
php scripts/picara environment change production
```

## Vendors and libraries used
* [ADODB](https://github.com/ADOdb/ADOdb) - PHP Database Abstraction Layer 
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - PHP Email Client
* [PhpLiteAdmin](https://www.phpliteadmin.org/) - PHP Admin tool to manage SQLite databases

These vendor libraries are included in this repository and shipped with the framework.

## Authors
**Arturo Lopez Perez** - Main and sole developer (so far).

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
