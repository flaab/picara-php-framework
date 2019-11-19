<h1 class="mb-3"><?= $modeldisplay ?> list</h1>

<!-- ==== Navbar for this model ==== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="navbar-collapse" id="navbarModel">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $link['controller'] ?>/insert" title="Create new record" tabindex="-1">Create</a>
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

<!-- ==== Start container ==== -->
<? if(!isset($pagination['empty'])): ?>
    
    <!-- ==== Pagination ==== -->
    <? include(BUILTIN_WEB_VIEW . 'Paginate/pagelinks.php'); ?>
    <!-- ==== Pagination ==== -->
    
    <!-- Begin results table -->
    <table class="table table-sm table-striped text-xsmall mt-4">
        <thead>
            <tr>
                <? foreach($fields as $field): ?>
                    <? if(!is_array($hidden_fields) || !in_array($field, $hidden_fields)): ?>
                        <th scope='col'><?= ucfirst($field) ?></th>
                    <? endif; ?>
                <? endforeach; ?>
                <th colspan="4"></th>    
            </tr>
        </thead>
        
        <!-- Begin result -->
        <tbody>
            <? foreach($pagination['collection'] as $model): ?>
                <? include($pagination['snap']); ?>
            <?  endforeach; ?>
        </tbody>
        <!-- End result -->
    
    </table>
    <!-- End results table -->
    
    <!-- Control -->
    <div class="container mb-5">
        <div class="row"> 
            <!-- Begin order form -->
            <div class="col">
                <form action="<?= $pagination['base_link'] ?>" method="post" class="form-inline">
                <label class="my-1 mr-2" for="inlineFormElements">Show</label>
                <select name="elements" class="custom-select my-1 mr-sm-2 " id="inlineFormElements">
                    <? for($it = 1; $it <= 50; $it++) { if($pagination['elements'] == $it) $selected = 'selected'; else $selected = '';?>
                        <option value="<?= $it ?>" <?= $selected ?>><?= $it ?></option>
                    <? } ?>
                </select> 
                <label class="my-1 mr-2" for="inlineFormOrder">Order By</label>
                <select name="order" class="custom-select my-1 mr-sm-2" id="inlineFormOrder">
                    <? foreach($fields as $field) { if($pagination['order'] == $field) $selected = 'selected'; else $selected = '';?>
                        <option value="<?= $field ?>" <?= $selected ?>><?= $field ?></option>
                    <? } ?>
                </select>
                <select name="orientation" class="custom-select my-1 mr-sm-2" id="inlineFormOrientation">
                        <? if($pagination['direction'] == 'ASC') $selected = 'selected'; else $selected = ''; ?>
                        <option value="ASC" <?= $selected ?>>Asc</option>
                        <? if($pagination['direction'] == 'DESC') $selected = 'selected'; else $selected = ''; ?>
                        <option value="DESC" <?= $selected ?>>Desc</option>
                </select>

                <input type="submit" name="change_elements" value="Go"  class="btn btn-outline-primary my-1">
                </form>
            </div>
            <!-- End order form -->

            <!-- Begin export form -->
            <div class="col ml-auto text-right">
                <form action="<?= $pagination['base_link'] . $pagination['page'] ?>" method="post" target="_blank" class="form-inline ml-auto">
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Export</label>
                    <select name="range" class="custom-select my-1 mr-sm-2" id="inlineFormRange">
                        <option value="this_page">This page</option>
                        <option value="all">All results</option>
                    </select>
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">to</label>
                    <select name="format" class="custom-select my-1 mr-sm-2" id="inlineFormFormat">
                        <option value="Json">Json</option>
                        <option value="Xml">Xml</option>
                        <option value="Yaml">Yaml</option>
                        <option value="Csv">Csv</option>
                    </select>
                    <input type="submit" name="export" value="Go" class="btn btn-outline-primary my-1">
                </form>
            </div>
            <!-- End export form -->
        </div>
    </div>
    
    <!-- ==== Pagination ==== --> 
    <? include(BUILTIN_WEB_VIEW . 'Paginate/pagelinks.php'); ?>
    <!-- ==== Pagination ==== --> 
<? endif; ?>
<!-- ==== End Container ==== -->
