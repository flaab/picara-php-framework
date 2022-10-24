<? if(!$executed): ?>
    <div class="jumbotron" style="text-align: center;">
    <h1 class="display-4"><?= ucwords($taskname); ?></h1>
    <p class="lead"><?= $description ?></p>
    </div>
<? endif; ?>

<? if(!$executed): ?>
    <!-- === Task not executed ==== -->
    <div class="card">
        <div class="card-header">
            <? _('Are you sure you want to run this task?') ?>
        </div>
        <div class="card-body pt-4">
            <form action="<?= $link['controller'] ?>/<?= $link['action'] ?>/<?= $function ?>" method="POST" class="sm">
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
                <a role="button" href="admin/tasks" class="btn btn-primary"><?= _('No, go back') ?></a>
                <button type="submit" name="submit" class="btn btn-danger" name="submit"><?= _('Yes, run this task') ?></button>
            </div>
            </form>
        </div>
    </div>
    <!-- === End Task not executed ==== -->
<? else: ?>
    <!-- === Task Executed ==== -->
    <div class="card mt-4">
        <div class="card-header">
            <?= _('Task') ?>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3"><?= _('Name') ?></dt>
                <dd class="col-sm-9"><?= $name ?></dd>
                <dt class="col-sm-3"><?= _('Description') ?></dt>
                <dd class="col-sm-9"><?= $description ?></dd>
            </dl>
            <a href="admin/tasks" class="btn btn-primary"><?= _('Back to Tasks') ?></a>
            <a href="admin/welcome" class="btn btn-secondary"><?= _('Back to Admin Site') ?></a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-sm-5">
            <div class="card mt-3">
                <div class="card-header">
                    <?= _('Inputs') ?>
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
                    <p><?= _('The task has no inputs.') ?></p>
                    <? endif; ?>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="card mt-3">
                <div class="card-header">
                    <?= _('Output') ?>
                </div>
                <div class="card-body">
                    <p><samp><?= nl2br($res) ?></samp></p>
                </div>
            </div>
        </div>
    </div>
    <!-- === End Task Executed ==== -->
<? endif; ?>
