<div class="jumbotron pl-5 pr-5 pb-5" style="text-align: center;">
    <h1 class="display-4">Welcome to the <strong>Admin Site</strong></h1>
    <p class="lead">It is a private, login-protected part of your website to scaffold your models and implement your back-end. You can make use of <strong>admin tasks</strong> and <strong>model actions</strong>, which are implemented in your code and permeate the admin site automatically.</p>
</div>
<div class="row">
    <div class="col-md-6">
        <h3 class="mt-3 mb-3">Customize this page</h3>
        <ul>
        <li>Customize this controller at <i><?= CONTROLLER ?>admin.php</i></li>
        <li>Customize this view at <i><?= VIEW ?>admin/welcome.php</i></li>
        <li>Customize the layout at <i><?= LAYOUT ?>admin.php</i></li>
        </ul>
        
        <h3 class="mt-4 mb-3">Edit users and permissions</h3>
        <ul>
        <li>Edit the file <i><?= USERCONFIG ?>adminsite.yml</i></li>
        </ul>
        <!--<h3 class="mt-5 mb-3">Scaffold your models</h3>
        <ul>
            <li>Scaffold a model
            <pre>php scripts/picara scaffold generate <i>model_name</i></pre></li>
            
            <li>Scaffold all models
            <pre>php scripts/picara scaffold all</pre>
        </ul>-->
        <h3 class="mt-4 mb-3">Create admin tasks</h3>
        <ul>    
        <li>Add a protected method to <i><?= USERLIB ?>myadmincontroller.php</i></li>
            <li>Add the method name to the property <i>$admin_tasks</i>.</li>
        </ul>
        <h3 class="mt-4 mb-3">Create model actions</h3>
        <ul>    
        <li>Add a public method to <i><?= USERLIB ?>yourmodel.php</i></li>
            <li>Add the method name to the property <i>$model_actions</i>.</li>
        </ul>
    </div>
    <div class="col-md-6">
        <? if(isset($other_scaffolds) && count($other_scaffolds) > 0): ?>
        <h3 class="mt-3 mb-3">Scaffolds</h3>
        <div class="list-group">
            <? foreach($other_scaffolds as $connection => $data): ?>
                <? foreach($data as $name => $arr): ?>
                <a href="<?= $arr['controller'] ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?= ucfirst($name) ?></h5>
                    <small class="text-muted">connection <strong><?= $connection ?></strong></small>
                </div>
                <p class="mb-1"></p>
                <small class="text-muted">Edit controller at <i><?= CONTROLLER ?><?= str_replace("-","_",$arr['controller']) ?>.php</i></small><br />
                <small class="text-muted">Edit model settings at <i><?= MODEL ?>model/<?= $name ?>.yml</i></small><br />
                <small class="text-muted">Edit views at <i><?= VIEW ?><?= str_replace("-","_",$arr['controller']) ?>/*</i></small>
            </a>
                <? endforeach; ?>
            <? endforeach; ?>
        </div>
        <? else: ?>
        <h3 class="mt-3 mb-3">Start building</h3>
        <ul>
            <li>Create a model
            <pre>php scripts/picara create model <i>model_name</i></pre></li>
            
            <li>Edit your model settings
            <pre>Edit the file <i><?= USERCONFIG ?>model/model_name.yml</i></li>
            
            <li>Scaffold a model
            <pre>php scripts/picara scaffold model <i>model_name</i></pre></li>
            
            <li>Scaffold all models
            <pre>php scripts/picara scaffold all</pre>
        </ul>
        <p>Existing and scaffolded models will be listed here for edition.</p>
        <? endif; ?>
    </div>
</div>
