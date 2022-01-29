<div class="jumbotron mb-5 pr-5 pl-5" style="text-align: center;">
<h1 class="display-4">Administration Tasks</h1>
<p class="lead">This panel allows you to execute site-wide administrative tasks or processes. To create a new task, implement a new method in <strong><?= USERCONFIG ?>myadmincontroller.php</strong> and append the method name to the <strong>$admin_tasks</strong> class property array. Available tasks are listed below, ready to be called.</p>
</div>
<div class="row">
    <div class="col-md-12">
<? if(is_array($tasks) && count($tasks) > 0): ?>
    <? foreach($tasks as $name => $task): ?>
    <div class="card mb-3">
        <div class="card-body">
        <a href="admin/runtask/<?= $name ?>" class="btn btn-primary float-right align-middle btn-lg mt-2"><strong>RUN TASK</strong></a>
            <h4 class="card-title"><?= ucwords($task['name']) ?></h4>
            <p class="card-text"><?= $task['description'] ?></p>
        </div>
    </div> 
    <? endforeach; ?>
<? else: ?>
<? endif; ?>
    </div>
</div>
