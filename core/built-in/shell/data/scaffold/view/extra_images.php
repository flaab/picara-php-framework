<h1 class="mb-3">Edit <?= $modeldisplay ?>  &nbsp; <small class="text-muted h5">Images</small> </h1>

<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $link['controller'] ?>/update/<?= $model['id'] ?>">Back</a>
            </li>
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
    <form method="POST" 
          enctype="multipart/form-data" 
          action="<?= $link['controller'] ?>/extra_images/<?= $model['id'] ?>" class="border p-4">
        <fieldset class="w-auto">
            <!-- Start image list -->
            <? if(empty($images)): ?>
                <p>No images yet.</p> 
            <? else: ?>
                <? for($it = 0; $it < count($images); $it++): ?>
                    <div style="float: left; margin: 25px;">
                        <a href="<?= $images[$it]['image'] ?>" target="_blank">
                            <img src="<?= $images[$it]['thumb'] ?>" border="0" class="img-thumbnail">
                        </a>
                        <br>
                        <input type="checkbox" name="delete[<?= $it ?>]"> Delete
                    </div>
                <? endfor; ?>
                <br>
                <div style="clear:both;">
            <? endif; ?>
            <!-- End image list -->
            <input type="file" name="<?= UPLOAD_FILE_INPUT_NAME ?>">
        </fieldset>
        <input class="btn btn-primary mt-4" type="submit" name="submit" value="Send">
    </form>
</div>
