<tr>
<?php

/**
* GENERAL SCAFFOLDING ELEMENT
*
* This is the "scaffolding modelsnap". His duty is to display the row for the represented object,
* following foreign key propagations to show human-understandable relationship values.
*
* (!) Override it with yours, it will execute much much faster
*/

$attributes = $model->getFields();
$fks = $model->getForeignFields();
$maxlen = 80;
$metadata = Pi_metadata::singleton();

// Foreach row
foreach($attributes as $var)
{
    if(!is_array($hidden_fields) || !in_array($var, $hidden_fields))
    {
        // Short up some strings to a max lenght
        if(strlen($model->fields->{$var}) > $maxlen)
        {
            $model->fields->$var = substr($model->fields->$var, 0, $maxlen) . "...";
            
        //Process to follow foreign key propagations and show related value instead of just a number ;-)
        } else if(in_array($var, $fks)){

            // Following the rules...we expect to get the className from the foreign key
            $relatedModel = $metadata->get_relationship_name_from_fk($pagination['model'], $var, true);
            
            // If model exist foreign key is confirmed
            if(Pi_loader::model_exists($relatedModel))
            {
                // Value must be numeric (if null, nothing displayed)
                if(is_numeric($model->fields->$var))
                {
                    // Attempt to create it
                    $relatedObject = new $relatedModel($model->fields->$var);
                    
                    // Check for any error
                    if($relatedObject->isOk())
                        $model->fields->$var = $relatedObject->getValueString();
                        
                } else {
                
                    // Foreign key is null, so nothing has to be displayed
                    $model->fields->$var = '';
                }
            }	
        }
        if($var == PRIMARY_KEY)
        { 
            echo('<th scope="row" class="align-middle">'. $model->fields->{$var} .'</th>');
        } else {
            echo('<td class="align-middle">' . stripslashes($model->fields->{$var}) . '</td>');
        }
    }
}
?>

<td class="align-middle text-right text-info" style="white-space: nowrap;">
    <a href="<?= $link['controller'] ?>/view/<?= $model->fields->id ?>" title="View">view</a> | 
    <a href="<?= $link['controller'] ?>/update/<?= $model->fields->id ?>" title="Edit">edit</a> | 
    <a data-toggle="modal" data-target="#exampleModalCenter<?= $model->fields->id ?>" href="#">delete</a> 
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter<?= $model->fields->id ?>" 
                tabindex="-1" role="dialog" aria-labelledby="ariaModalCenterTitle<?= $model->fields->id ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-dark" id="ariaeModalLongTitle<?= $model->fields->id ?>">Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-dark">
          Are you sure you want to delete this record?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <a href="<?= $link['controller'] ?>/delete/<?= $model->fields->id ?>" title="Delete">
            <button type="button" class="btn btn-danger">Yes, delete this sucker</button>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal -->
    <? if(isset($navigate_has_many) && count($navigate_has_many) > 0): ?>
        | <a class="dropdown-toggle" 
           href="#" title="Related" 
           role="button" id="dropdownActionGoTo<?= $model->fields->id ?>"
           data-toggle="dropdown" 
           aria-haspopup="true" 
           aria-expanded="false">related</a>&nbsp;
           <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownActionGoTo<?= $model->fields->id ?>">
           <? foreach($navigate_has_many as $hasmodel => $target): ?>
                <a class="dropdown-item small" href="<?= $target ?>/by/<?= strtolower($modelname) ?>/<?= $model->fields->id ?>"><?= ucwords($hasmodel) ?> list</a>
            <? endforeach; ?>
          </div>
    <? endif; ?>
    <? if(isset($model->model_actions) && is_array($model->model_actions) && count($model->model_actions) > 0): ?>
        | <a class="dropdown-toggle" 
           href="#" title="Actions" 
           role="button" id="dropdownActionMenu<?= $model->fields->id ?>"
           data-toggle="dropdown" 
           aria-haspopup="true" 
           aria-expanded="false">actions</a>&nbsp;
           <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownActionMenu<?= $model->fields->id ?>">
             <? foreach($model->model_actions as $function => $data): ?>
                <a class="dropdown-item small" href="<?= $link['controller'] ?>/action/<?= $model->fields->id ?>/<?= $function ?>"><?= ucwords($data['name']) ?></a>
            <? endforeach; ?>
          </div>
    <? endif; ?>
</td>
</tr>
