<div class="jumbotron pt-5 pb-5 pl-5 pr-5" style="text-align: left;">
    <? if(count($fix) == 0): ?>
        <h1 class="display-4">It works, you are running!</h1>
        <p class="lead">This project is named <strong><?= TITLE ?></strong>. Edit app name and settings at <strong><?= USERCONFIG ?>application.php</strong>.</p>
        <hr class="my-4">
        <p class="lead">
            <a class="btn btn-primary btn-lg" href="admin/welcome" role="button">Site Administration</a>
            <a class="btn btn-info btn-lg ml-3" href="assets/phpliteadmin/phpliteadmin.php" target="_blank" role="button">PHPLiteAdmin</a>
            <a class="btn btn-secondary btn-lg ml-3" href="assets/phpinfo.php" target="_blank" role="button">PHPInfo</a>
            <a class="btn btn-light btn-lg ml-3" href="https://github.com/flaab/picara-php-framework" target="_blank" role="button">GitHub</a>
        </p>
    <? else: ?>
        <h1 class="display-4">You're almost there!</h1>
        <p class="lead">In order for the framework to be fully operative, these issues need your attention.</p>
        <hr class="my-4">
        <ul>
            <? foreach($fix as $f): ?>
            <li><?= $f ?></li>
            <? endforeach; ?>
        </ul>
    <? endif; ?>
</div>
<h3 class="mt-5">Customize this page</h3>
<ul>
<li>Customize this controller at <i><?= CONTROLLER ?>index.php</i></li>
<li>Customize this view at <i><?= VIEW ?>index.php</i></li>
<li>Customize this layout at <i><?= LAYOUT ?>default.php</i></li>
</ul>
<h3 class="mt-5">Connect to a database</h3>
<ol>
    <li>Create a database connection
        <pre><small>php scripts/picara create connection main -adapter=(mysql|postgres|oci8|sqlite) -host=localhost -db=db_name -user=my_user -password=mypassword</small></pre></li>
    <li>Test the connection 
        <pre>php scripts/picara test connection main</pre></li>
    <li>Create additional connections if needed
        <pre>php scripts/picara create connection connection_name</pre></li>
        <li>Edit connection settings at <i><?= USERCONFIG ?>db/connection_name.yml</i></li>
</ol>

<h3 class="mt-5">Handle database environments</h3>
<ul>
    <li>Get the current environment
        <pre>php scripts/picara environment</pre></li>
    
    <li>Change to other environment
        <pre>php scripts/picara environment change (production|development|testing)</pre></li>
</ul>

<h3 class="mt-5">Start building</h3>
<ul>
    <li>List all elements of this application
    <pre>php scripts/picara list all</pre></li>

    <li>Create a model
    <pre>php scripts/picara create model model_name --connection="main" --table="table_name"</pre></li>
    
    <li>Scaffold a model
    <pre>php scripts/picara scaffold generate model_name</pre></li>
    
    <li>Scaffold all models
    <pre>php scripts/picara scaffold all</pre>

    <li>Create a web controller
    <pre>php scripts/picara create controller controller_name</pre></li>

    <li>Create a shell controller</li>
    <pre>php scripts/picara create shell controller_name</pre></li>
    
    <li>Create an admin controller</li>
    <pre>php scripts/picara create admincontroller controller_name</pre></li>
    
    <li>Create a custom log</li>
    <pre>php scripts/picara create log log_name</pre></li>
</ul>

<h3  class="mt-5">Customize your components</h3>
<ul>
    <li>Add shared Model methods at <i>app/lib/mymodel.php</i></li>
    <li>Add shared Controller methods at <i>app/lib/mycontroller.php</i></li>
    <li>Add shared Web Controller methods at <i>app/lib/mywebcontroller.php</i></li>
    <li>Add shared Shell Controller methods at <i>app/lib/myshellcontroller.php</i></li>
    <li>Add shared Admin Controller methods at <i>app/lib/myadmincontroller.php</i></li>
</ul>
