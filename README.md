# Picara PHP Web Development Framework 
A self-contained PHP rapid development framework for MVC http/rest/cli applications, developed and used since 2007. Heavily opinionated, it focuses on convention over configuration and implements implicit routing. It offers a flexible admin site from scratch, rich scaffolding and libraries for cache generation, queries, lang files, pagination, sessions, validation, forms, email, logs, images, file uploads, static pages and more. 
 

### What makes it different?
Picara is a breeze of fresh air in a world of heavy and large frameworks. It is a self-contained framework that can be installed simply by cloning a git repository. It requires no composer dependencies and uses no middleware, and yet, it is as capable and fast as other, full-blown, complex alternatives.


### What can it be used for?
It can be used to build anything. Simple relational websites such as as blogs or stores, complex relational RestFul API services or high-traffic relational websites such as newspapers or online services. The framework implements a comprehensive cache system that can serve static files generated from your routes, which allows it to handle tons of requests without raising the database layer or loading the application libraries.


### Fast development and deployment
The framework is ready to rock right after cloning the repository, with SQLite activated as default relational database system. The project can be developed locally using the PHP Development Server and deployed to a standalone virtual host or a sub-directory inside an existing domain. 


## Main Features
- Strict MVC architecture
- Supports SQLite, MySQL, MariaDB, PostgreSQL and Oci8
- Supports has_one, has_many, belongs_to and has_and_belongs_to_many relationships
- Model relationships are inferred from table names or declared in the model config files
- Built-in full-text search engine, capable of navigating relationships to produce results
- YAML Lang file support for multi-language sites
- Automatic and customizable Admin Site
- Model and Controller callbacks
- Comprehensive scaffolding

### Other features
- You can create HTTP and CLI controllers that access the same resources
- You can create RESTful API controllers and define acceptable request types
- You can create administration tasks which are easily executed from the *Admin Site*
- You can create model actions which are easily executed from the *Admin Site*
- Implicit routing: there is no need to define routes for each method
- Explicit routing is allowed if you wish to define your own routes
- Built-in recordset exportability to json, xml, yml and csv
- It can handle multiple and different database connections
- HTTP Controllers have built-in session and IP controls
- Create as many logs as you need in your application
- Rich and abundant model validation methods
- Send emails via SMTP or Sendmail

## Requirements
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

Follow these steps to install and run the framework in your machine.

1. Open a terminal in your projects folder
2. Execute the following command to clone the repository
```
git clone https://github.com/flaab/picara-php-framework.git
```
3. Change to the working directory
```
cd picara-php-framework
```
4. Execute the script to set proper folder permissions
```
bash scripts/setup_permissions.sh
```
5. Start the PHP Developer Server
```
bash scripts/runserver.sh
```
Now point your browser to http://localhost:8000 to be greeted.

![Picara running on PHP Development Server](https://www.dropbox.com/s/bxzev2731en9p1b/picara_welcome_php.png?raw=1)


Optionally, you can serve the project using Apache.

1. Move the *picara-php-framework* folder to your Apache DocumentRoot directory.
2. Start Apache Server in your computer.

That's it. Point your browser to http://localhost/picara-php-framework/htdocs.


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
    - **html/** -> Contains all html files of the application.
        - **message/** -> Code snippets to display errors, validation errors and success messages.
        - **mod/** -> This folder is meant to store repetitive html snippets shared across the web application.
        - **modelsnap/** -> Contains the html snippet to display model records in the web applications.
        - **layout/** -> Contains the different layouts of the web application.
        - **pages/** -> Contains the static pages the framework can serve via the built-in static controller.   
        - **templates/** -> Contains html templates to generage custom cache files.
        - **searchsnap/** -> Contains the html snippets to display model records when using the search engine.
        - **view/** -> Contains views of the web application.
    - **lang/** -> Contains the YML lang-files for the web application, if multilang is enabled.
    - **lib/** -> Contains libraries of the application. Place any third-party libraries here.
    - **log/** -> Contains log-files of the application.
    - **model/** -> Contains the models of the application.
    - **shell/** -> Contains the cli/shell controllers of the application.
- **core/** -> Contains the core of the framework.
	- **actions/** -> Contains basic actions of the framework.
	- **built-in/** -> Contains built-in cli/http controllers of the framework.
	- **config/** -> Contains internal config files of the framework.
	- **lib/** -> Contains libraries of the framework.
	- **system/** -> Contains system libraries of the framework.
	- **utils/** -> Contains utilities of the framework.
	- **vendors/** -> Contains third-party libraries and dependencies.
- **htdocs/** -> Contains the document root of the web application.
	- **assets/** -> Contains files that can be served via http request.
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

## The Site Administration Panel
The framework provides a built-in, authenticated site administration panel. From it you can:
- Navigate your scaffolded models and relationships
- Run administration tasks for your project
- Run model actions for any given record
- Examine logs

### Login
Point your browser to the admin panel, located at http://localhost:8000/admin/login and log in. The default username is **admin** and the default password is **mypassword**. You can add, edit or delete users by editing the file located at *app/config/adminusers.php*. This file holds the users that can log into the admin panel, and should not be confused with your website users, should you have any in the future.

![The Site Admin Login Page](https://www.dropbox.com/s/szsojmbpwwvk776/admin0.png?raw=1)

Once logged-in, you'll be greeted with friendly how-tos and be able to navigate.

![The Site Admin Welcome Page](https://www.dropbox.com/s/tlgaxsfpbx4rzya/%20admin1.png?raw=1)


### Administration Tasks
The whole point of having a site administration panel is to perform routinary administration tasks, and this is made easy by the framework. Any custom administrative function can be called from the admin site, with customisable inputs and validation, without creating controllers or views. 

Administration tasks must be declared as methods in **app/config/myadmincontroller.php** and append the method name to the **$admin_tasks** class property array. To view all the available tasks, go to **Tools->Tasks** or http://localhost:8000/admin/tasks. The framework implements some example tasks for your examination such as *Hello World, Custom Greet and Simple Sum*.

![The Site Admin Tasks Page](https://www.dropbox.com/s/hzp3upvpg1f6i1b/admin3.png?raw=1)


### Logs
Logs can be navigated from the admin panel as well. The framework can handle an unlimited number of log files.

To view all the available tasks, go to **Tools->Logs** or http://localhost:8000/admin/logs.

![The Site Admin Logs Page](https://www.dropbox.com/s/wemkhhjl9ee40qf/admin2.png?raw=1)


### More than scaffolding
This framework was developed for rapid development of content sites and implements the most comprehensive scaffolding that I am aware of, and it is also easily customizable. It implements a full-text search engine and relationships are navigatable. Insert and edition forms support full-text WYSIWYG inputs with CKEditor5, image uploads and file uploads. Validation rules are read from the model config files. You must be logged-in to access the scaffolding controllers. Any recordset can be exported in JSON, CSV, YAML and XML formats, even those resulting from a search.

The following are out-of-the-box scaffolding screenshots for a sample blog application with just <i>authors, categories, tags and posts</i> models, without any addition, removal or customization of code in the generated scaffolding controllers.

Listing all categories in our sample blog.

![Scaffolding: Category List](https://www.dropbox.com/s/bc7oydtmq7z6j6t/admin5.png?raw=1)

Editing an author in our sample blog.

![Scaffolding: Insert a new Author with avatar](https://www.dropbox.com/s/28940dtm8w5zqke/admin4.png?raw=1)

Editing a post in our sample blog.

![Scaffolding: Insert a new Post with featured image](https://www.dropbox.com/s/v302r72i0gxtff5/admin6.png?raw=1)


## Deployment to production
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
- Delete the phpinfo.php file located at htdocs/assets/phpinfo.php
- Delete or rename the PhpLiteAdmin folder located at htdocs/assets/phpliteadmin
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

## Vendors and libraries
Special thanks to the authors of the following resources.
* [PhpLiteAdmin](https://www.phpliteadmin.org/) - PHP tool to manage SQLite databases
* [ADODB](https://github.com/ADOdb/ADOdb) - PHP Database Abstraction Layer 
* [PHPMailer](https://github.com/PHPMailer/PHPMailer) - PHP Email Client

These vendor libraries are included in this repository and shipped with the framework.

## Resources 
Special thanks to the authors of the following resources.
* [Bootstrap](https://getbootstrap.com/) - CSS, JS and component library.
* [CKEditor5](https://ckeditor.com/ckeditor-5/) - WYSIWYG framework for rich html forms. 

## Todo and Roadmap
- Structural changes needed to group models, controllers and views into re-usable Apps or Modules
- Improvements to the Admin Site implementing different permission groups
- A native authentication library extending the session library
- A built-in restful api controller
- A migration library

Feel free to contribute on the development of this framework.


## Authors
**Arturo Lopez Perez** - Main and sole developer (so far).


## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
