<h1 class="mb-3"><?= $modeldisplay ?> files <small class="text-muted h5"><?= $block ?></small></h1>

<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <? if(Pi_session::check_permission($modelname,'update')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link['controller'] ?>/update/<?= $model['id'] ?>">Back</a>
                </li>
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

<!-- Start images form -->
<div class="container-fluid">
<form method="POST" enctype="multipart/form-data" action="<?= $link['controller'] ?>/block/<?= $model['id'] ?>/<?= $block ?>"  class="border p-4">
    <fieldset  class="w-auto">
    <!-- Start file list -->
    <? if(empty($files)): ?>
        <p>No files yet.</p> 
    <? else: ?>
        <? for($it = 0; $it < count($files); $it++): ?>
                <input type="checkbox" name="delete[<?= $it ?>]" style="margin: 0px;">
                <a href="<?= $files[$it] ?>"><?= preg_replace("/^\/?([^\/]+\/)+/", '', $files[$it]) ?></a>
                <br>
        <? endfor; ?>
        <br>
    <? endif; ?>
    <!-- End image list -->
    <input type="file" name="<?= UPLOAD_FILE_INPUT_NAME ?>">
    </fieldset>
    <input type="submit" name="submit" class="btn btn-primary mt-4" value="Submit">
</form>
</div>
<!-- End images form -->
