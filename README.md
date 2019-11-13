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

## Install

Open a terminal in your apache document root folder and clone the repository.
```
git clone https://github.com/flaab/picara-php-framework.git
```

## Check Installation

Point your browser to http://localhost/picara-php-framework/htdocs and check if it loads properly. 


## Deployment

Create and activate an Apache VirtualHost that points to the htdocs/ folder, and you are ready to go. 
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
If you are using HTTPS, define the Apache VirtualHost as follows instead.
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
## Built With

* [ADODB](https://github.com/ADOdb/ADOdb) - Database Abstraction Layer 
* [PHPMAILER](https://github.com/PHPMailer/PHPMailer) - PHP Email Client


## Authors

**Arturo Lopez Perez** - Main and sole developer (so far).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
