# Picara PHP Web Development Framework 
A self-contained PHP rapid development framework for MVC http/rest/cli applications, developed and used since 2007. It focuses on convention over configuration and implements implicit routing. It offers a flexible admin site from scratch, rich scaffolding and libraries for cache generation, queries, lang files, pagination, sessions, validation, forms, email, logs, images, file uploads, static pages and more. 
 

### What makes it different?
Picara is a breeze of fresh air in a world of heavy and large frameworks. It is a self-contained framework that can be installed simply by cloning a git repository. It requires no composer dependencies and uses no middleware, and yet, it is just as powerful and fast as other, full-blown and complex alternatives.


### What can it be used for?
It can be used to build anything. Simple relational websites such as as blogs or stores, complex relational RestFul API services or high-traffic relational websites such as newspapers or online services. The framework implements a comprehensive cache system that can serve static files generated from your routes, which allows it to handle tons of requests without raising the database layer or loading the application libraries.


## Main Features
- Strict MVC architecture
- Supports SQLite, MySQL, MariaDB, PostgreSQL and Oci8
- Supports has_one, has_many, belongs_to and has_and_belongs_to_many relationships
- Model relationships are inferred from table names, or declared in the models
- YAML Lang file support for multi-language sites
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
- Build-in recordset exportability to json, xml, yml and csv
- It can handle multiple and different database connections
- HTTP Controllers have built-in session and IP controls
- Create as many logs as you need in your application
- Rich and abundant model validation methods
- Send emails via SMTP or Sendmail

## Requirements for production
- Apache Server > 2.2 *(for production only)*
- SQLite > 3.0
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
The framework is self-contained. No composer or package manager is needed. 

Follow these steps to install and run the framework in your machine, without Apache.

1. Open a terminal
2. Navigate to your projects folder
3. Execute the following command to clone the repository
```
git clone https://github.com/flaab/picara-php-framework.git
```
4. Change to the working directory
```
cd picara-php-framework
```
5. Execute the script to set proper folder permissions
```
bash scripts/setup_permissions.sh
```
6. Start the PHP Developer Server
```
bash scripts/runserver.sh
```
Now point your browser to http://localhost:8000 to see the homepage of the project.

![Picara running on PHP Development Server](https://www.dropbox.com/s/27loxhicc0n55td/picara_welcome_php.png?raw=1)


Optionally, you can serve the project using Apache in your localhost.

1. Move the *picara-php-framework* folder to your Apache DocumentRoot directory.
2. Start Apache in your computer.

That's it. Point your browser to http://localhost/picara-php-framework/htdocs.

![Picara running on Apache Server](https://www.dropbox.com/s/w6hqsosh4v63oa2/picara_welcome_apache.png?raw=1)


## Directory Structure
The directory structure of the repository is as follows.

- **app/** -> This folder contains your application code. You will never have to write or edit code outside this folder.
    - **cache/** -> Contains user generated cache files, generated from templates.
    - **config/** -> Contains application configuration files.
    	- **connection/** -> Contains the database connections of the application.
		- **model/** -> Contains config files of the models of the application.
		- *adminusers.php* -> List of users for the admin panel.
		- *application.php* -> Stores application constants and options.
		- *environment.php* -> Stores the application execution environment.
		- *langs.yml* -> Stores a list of activated languages for web application.
		- *routes.yml* -> Stores additional/custom routes of the web application.
    - **controller/** -> Contains the http/web controllers of the application.
    - **lang/** -> Contains the YML lang-files for the web application, if multilang is enabled.
    - **layout/** -> Contains the different layouts of the web application.
    - **lib/** -> Contains libraries of the application. Place any third-party libraries here.
    - **log/** -> Contains log-files of the application.
    - **message/** -> code snippets to display errors, validation errors and success messages.
    - **mod/** -> This folder is meant to store repetitive html snippets shared across the web application.
    - **model/** -> Contains the models of the application.
    - **modelsnap/** -> Contains the html snippet to display model records in the web applications.
    - **pages/** -> Contains the static pages the framework can serve via the built-in static controller.   
    - **searchsnap/** -> Contains the html snippets to display model records when using the search engine.
    - **shell/** -> Contains the cli/shell controllers of the application.
    - **templates/** -> Contains html templates to generage custom cache files.
    - **view/** -> Contains views of the web application.
- **core/** -> Contains the core of the framework.
	- **actions/** -> Contains basic actions of the framework.
	- **built-in/** -> Contains built-in cli/http controllers of the framework.
	- **config/** -> Contains internal config files of the framework.
	- **lib/** -> Contains libraries of the framework.
	- **system/** -> Contains system libraries of the framework.
	- **utils/** -> Contains utilities of the framework.
	- **vendors/** -> Contains third-party libraries and dependencies.
- **htdocs/** -> Contains the document root of the web application.
	- **webroot/** -> Contains files that can be served via http request.
		- **css/** -> Stores the css files of the web application.
		- **images/** -> Stores the images of the web application.
		- **js/** -> Stores the javascript files of the web application.
		- **model_files/** ->  Stores public images and files associated to model records.
		- **pages/** -> Stores 503, 405 404, 403, 401 and 400 error pages of the web application.
		- **phpliteadmin/** -> This directory holds PHPLiteAdmin, to manage SQLite databases.
		- *phpinfo.php* -> This is the PHPInfo page.
	- *index.php* -> This file serves the web application from the http server.
- **resources/** -> Contains application resources.
	- **cache/** -> Contains files for the http cache system.
	- **db/** -> Contains sqlite databases of the application.
	- **tmp/** -> Contains temp files of the cache system.
- **scripts/** -> Contains executable scripts to use during development.


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

## Develop your application
Develop your application in the testing environment. Use the scripts described above whenever possible.

## Deployment
Deployment should be done using Apache Server.

### 1. Create a VirtualHost in your production server
Create an Apache VirtualHost that points to the htdocs/ folder of the framework project.
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
To serve the page via HTTPS, define your VirtualHost as follows instead.
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
Make sure the VirtualHost is serving the htdocs folder and not the parent folder.

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
* [PhpLiteAdmin](https://www.phpliteadmin.org/) - PHP tool to manage SQLite databases
* [ADODB](https://github.com/ADOdb/ADOdb) - PHP Database Abstraction Layer 
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - PHP Email Client

These vendor libraries are included in this repository and shipped with the framework.


## Todo and Roadmap
Next iterations of the framework will include:
- Structural changes needed to group models, controllers and views into re-usable Apps or Modules
- Improvements to the Admin Site implementing different permission groups
- A native authentication library extending the session library

Feel free to contribute on the development of this framework.


## Authors
**Arturo Lopez Perez** - Main and sole developer (so far).


## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
