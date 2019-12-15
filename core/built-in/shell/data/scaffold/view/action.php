<? if(!$executed): ?>
<h1 class="mb-3"><?= $modeldisplay ?> action  <small class="text-muted h5">for record with id = <?= $model['id'] ?></small></h1>
    <div class="card mb-4 mt-4">
        <div class="card-header">
            Action Details
        </div>
        <div class="card-body pt-4">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9"><?= $actionname ?></dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9"><?= $description ?></dd>
            </dl>
        </div>
    </div>
<? endif; ?>

<? if(!$executed): ?>
    <!-- === Task not executed ==== -->
    <div class="card">
        <div class="card-header">
            Are you sure you want to execute this action?
        </div>
        <div class="card-body pt-4">
            <form action="<?= $link['controller'] ?>/<?= $link['action'] ?>/<?= $model['id'] ?>/<?= $function ?>" method="POST" class="sm">
            <? if(isset($params) && count($params) > 0): ?>
                <? foreach($params as $i => $p): ?>
                <div class="form-group row">
                    <label for="email_address" class="col-md-4 col-form-label text-md-right">
                        <?= $p ?>
                    </label>
                    <div class="col-md-6">
                        <? if(is_null($classes[$i])): ?>
                            <input type="text" id="username" value="<?= $_POST['params'][$i] ?>" class="form-control" name="params[<?= $i ?>]" required autofocus>
                        <? else: ?>
                            <? Form::createSelectMenu($classes[$i]['name'], $classes[$i]['field'], "params[". $i ."]", $_POST['params'][$i]); ?>  
                        <? endif; ?>
                    </div>
                    </div>
                <? endforeach; ?>
            <? endif; ?>
            <div class="col-md-6 offset-md-4">
                <a role="button" href="<?= $link['controller'] ?>/view/<?= $model['id'] ?>" class="btn btn-primary">No, go back</a>
                <button type="submit" name="submit" class="btn btn-danger" name="submit">Yes, run this action</button>
            </div>
            </form>
        </div>
    </div>
    <!-- === End Task not executed ==== -->
<? else: ?>
    <!-- === Task Executed ==== -->
    <div class="card mt-4">
        <div class="card-header">
            Action
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9"><?= $actionname ?></dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9"><?= $description ?></dd>
            </dl>
            <a href="<?= $link['controller'] ?>/view/<?= $model['id'] ?>" class="btn btn-primary">Back to details</a>
            <a href="<?= $link['controller'] ?>" class="btn btn-secondary">Back to list</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-sm-5">
            <div class="card mt-3">
                <div class="card-header">
                    Inputs
                </div>
                <div class="card-body">
                    <? if(count($params) > 0): ?>
                    <dl class="row">
                        <? foreach($params as $i => $p): ?>
                        <dt class="col-sm-6"><?= $p ?></dt>
                        <dd class="col-sm-6"><?= $_POST['params'][$i] ?></dd>
                        <? endforeach; ?>
                    </dl>
                    <? else: ?>
                    <p>The action has no inputs.</p>
                    <? endif; ?>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="card mt-3">
                <div class="card-header">
                    Output
                </div>
                <div class="card-body">
                    <p><samp><?= nl2br($res) ?></samp></p>
                </div>
            </div>
        </div>
    </div>
    <!-- === End Task Executed ==== -->
<? endif; ?>
