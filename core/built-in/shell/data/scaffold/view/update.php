<h1 class="mb-3">Edit <?= $modeldisplay ?>  <small class="text-muted h5">with id = <?= $model['id'] ?></small></h1>
<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <? if(Pi_session::check_permission($modelname,'list')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link['controller'] ?>" title="Back" tabindex="-1">Back</a>
                </li>
            <? endif; ?>
            <? if(Pi_session::check_permission($modelname,'update')): ?>
                <? if($extra_images): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link['controller'] ?>/extra_images/<?= $model['id'] ?>" title="Extra Images" tabindex="-1">Extra Images</a>
                </li> 
                <? endif; ?>
                <!-- Begin file blocks -->
                <? if(is_array($blocks)): ?>
                    <? foreach($blocks as $block): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link['controller'] ?>/block/<?= $model['id'] ?>/<?= $block ?>"><?= ucfirst($block) ?></a>
                        </li>
                    <? endforeach; ?>
                <? endif; ?>
                <!-- End file blocks -->
            <? endif; ?>
            <? if(Pi_session::check_permission($modelname,'actions')): ?>
                <? if(isset($model_actions) && is_array($model_actions) && count($model_actions)): ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle nav-link" 
                       href="#" title="Delete" 
                       role="button" id="dropdownActionMenu"
                       data-toggle="dropdown" 
                       aria-haspopup="true" 
                       aria-expanded="false">Actions</a>
                        <div class="dropdown-menu" aria-labelledby="dropdownActionMenu">
                            <? foreach($model_actions as $function => $data): ?>
                                <a class="dropdown-item" href="<?= $link['controller'] ?>/action/<?= $model['id'] ?>/<?= $function ?>"><?= ucwords($data['name']) ?></a>
                            <? endforeach; ?>
                        </div>
                    </li>
                <? endif; ?>
            <? endif; ?>
        </ul>
        <? if($is_searchable): ?>
        <form class="form-inline my-2 my-lg-0" action="<?= $link['controller'] ?>/search" method="POST">
        <input class="form-control mr-sm-2" type="search" value="<?= $nice_search  ?>" 
                    name="search" aria-label="Search">
            <button class="btn btn-outline-primary my-2 my-sm-0" name="search_button" type="submit">Search</button>
        </form>
        <? endif; ?>
    </div>
</nav>
<!-- ==== End nabvar ==== -->

<div class="container-fluid">
<?php Pi_scaffold::createform($modelname, "update", $model['id'], $model, $n_m); ?>
</div>
