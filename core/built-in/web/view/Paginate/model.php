<h1><?= $pagination['display'] ?></h1>
<? if(isset($pagination['empty'])): ?>
    <p>No results found.</p>
<? else: ?>
	<? include('pagelinks.php'); ?>
    <? foreach($pagination['collection'] as $model): ?>
        <? include($pagination['snap']); ?>
    <? endforeach; ?>	
	<?php include('pagelinks.php'); ?>
<? endif; ?>	
