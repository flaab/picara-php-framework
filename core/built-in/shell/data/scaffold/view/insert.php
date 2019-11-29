<h1 class="mb-3">New <?= $modeldisplay ?></h1>

<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $link['controller'] ?>" title="Back" tabindex="-1">Back</a>
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

<div class="container-fluid">
<?php Pi_scaffold::createform($modelname, 'insert', NULL, NULL, $n_m); ?>
</div>
