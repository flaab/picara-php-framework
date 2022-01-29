<?php

/**
* PageLinks for ajax pagination
*/

// Not shown if last page 1
if($pagination['last_page'] != 1)
{
    if(DEFAULT_LANG == 'en') { $first = 'First'; $last = 'Last'; } elseif(DEFAULT_LANG == 'es') { $first = 'Primera'; $last = 'Última'; }
?>
<ul class="pgn">
	
    <? if($pagination['page'] > 1): ?>
        <li>
            <a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link'] . $pagination['first_page'] ?>'); return false;"><?= $first ?></a>
        </li>
    <? endif; ?>

	<? if($pagination['page'] > $pagination['first_page']): ?>
	 	<li><a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link'] . ($pagination['page']-1) ?>'); return false;"><</a></li>
	<? endif; ?> 

	<? foreach($pagination['left_links'] as $page): ?>
	    <li><a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link'] . $page ?>'); return false;"><?= $page ?></a></li>
    <? endforeach; ?>
    
	<li class="active"><a href="#" onclick="return false;"><?= $pagination['page'] ?></a></li>

	<? foreach($pagination['right_links'] as $page): ?>
	    <li><a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link'] . $page ?>'); return false;"><?= $page ?></a></li>
    <? endforeach; ?>

    <? if($pagination['page'] < $pagination['last_page']): ?>
	    <li><a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link']. ($pagination['page']+1) ?>'); return false;">></a></li>
    <? endif; ?>

    <? if($pagination['page'] < $pagination['last_page']): ?>
	    <li>
            <a href="#" onclick="load_div('<?= $pagination['div'] ?>','<?= $pagination['base_link'] . $pagination['last_page'] ?>'); return false;"><?= $last ?></a>
        </li>
    <? endif; ?>
</ul>
<br style="clear: both;">
<?php
}
?>
