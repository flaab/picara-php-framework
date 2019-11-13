<h1 class="mb-3"><?= $modeldisplay ?> details  <small class="text-muted h5">with id = <?= $model['Data']['id'] ?></small></h1>

<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $link['controller'] ?>" title="Back" tabindex="-1">Back</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $link['controller'] ?>/update/<?= $model['Data']['id'] ?>" title="Edit" tabindex="-1">Edit</a>
            </li>
            <li class="nav-item">
                <a data-toggle="modal" data-target="#exampleModalCenter<?= $model['Data']['id'] ?>" class="nav-link" href="#" title="Delete" tabindex="-1">Delete</a>
            </li>
            <? if(isset($navigate_has_many) && count($navigate_has_many) > 0): ?>
            <li class="nav-item dropdown">
                <a class="dropdown-toggle nav-link" 
                   href="#" title="Go to" 
                   role="button" id="dropdownActionGoTo"
                   data-toggle="dropdown" 
                   aria-haspopup="true" 
                   aria-expanded="false">Related</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownActionGoTo">
                        <? foreach($navigate_has_many as $hasmodel => $target): ?>
                            <a class="dropdown-item" href="<?= $target ?>/by/<?= strtolower($modelname) ?>/<?= $model['Data']['id'] ?>"><?= ucwords($hasmodel) ?> list</a>
                        <? endforeach; ?>
                    </div>
                </li>
            <? endif; ?>
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
                            <a class="dropdown-item" href="<?= $link['controller'] ?>/action/<?= $model['Data']['id'] ?>/<?= $function ?>"><?= ucwords($data['name']) ?></a>
                        <? endforeach; ?>
                    </div>
                </li>
            <? endif; ?>
        </ul>
        <form class="form-inline my-2 my-lg-0" action="<?= $link['controller'] ?>/search" method="POST">
        <input class="form-control mr-sm-2" type="search" value="<?= $nice_search  ?>" 
                    name="search" aria-label="Search">
            <button class="btn btn-outline-primary my-2 my-sm-0" name="search_button" type="submit">Search</button>
        </form>
    </div>
    
<!-- Modal -->
    <div class="modal fade" id="exampleModalCenter<?= $model['Data']['id'] ?>" 
                tabindex="-1" role="dialog" aria-labelledby="ariaModalCenterTitle<?= $model['Data']['id'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ariaeModalLongTitle<?= $model['Data']['id'] ?>">Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          Are you sure you want to delete this record?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <a href="<?= $link['controller'] ?>/delete/<?= $model['Data']['id'] ?>" title="Delete">
            <button type="button" class="btn btn-danger">Yes, delete this sucker</button>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal -->
</nav>
<!-- ==== End nabvar ==== -->

<div class="container-fluid">
    <!-- ==== Data start ==== -->
    <? foreach($groups as $gkey): ?>
        <? if(isset($model[$gkey]) && count($model[$gkey]) > 0): ?>
            <fieldset class="border p-4">
                <legend  class="w-auto"><?= $gkey ?></legend>
                    <? foreach($model[$gkey] as $key => $value): ?>
                    <dl class="row">
                        <dt class="col-sm-3"><?= ucwords($key) ?></dt>
                        <dd class="col-sm-9"><?= nl2br($value); ?></dd>
                    </dl>
                    <? endforeach; ?>
            </fieldset> 
        <? endif; ?>
    <? endforeach; ?>
    <!-- ==== DAta End ==== -->

    <!-- ==== Img start ==== -->
    <? if(is_array($images['main'])  || (is_array($images['extra']) && count($images['extra']) > 0)): ?>
        <fieldset class="border p-4">
            <legend  class="w-auto">Images</legend>
                <? if(is_array($images['main'])): ?>
                    <div style="float: left; margin: 25px;">
                        <a href="<?= $images['main']['image'] ?>" target="_blank">
                            <img src="<?= $images['main']['thumb'] ?>" border="0" class="img-thumbnail">
                        </a>
                    </div>
                <? endif; ?>
                <? if(is_array($images['extra'])): ?>
                    <? for($it = 0; $it < count($images['extra']); $it++): ?>
                    <div style="float: left; margin: 25px;">
                        <a href="<?= $images['extra'][$it]['image'] ?>" target="_blank">
                            <img src="<?= $images['extra'][$it]['thumb'] ?>" border="0" class="img-thumbnail">
                        </a>
                    </div>
                    <? endfor; ?>
                <? endif; ?>
        </fieldset>
    <? endif; ?>
    <!-- ==== Img end ==== -->
</div>
