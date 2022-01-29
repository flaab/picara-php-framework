<?php

/**
 * VIEW -> Pagination -> index
 * It will load the modelSnap for each object received from the controller
 */

?>

<h1>Search results for '<?= $searchQuery ?>' in <?= $pagination['display'] ?></h1>
<?php

if(isset($pagination['empty']))
{
	echo "<p>No results found.</p>";

} else {

?>

	<h3>Showing <b><?= $pagination['first_result'] ?></b> to <b><?= $pagination['last_result'] ?></b> from <b><?= $pagination['total_results'] ?></b> results</h3>
	
	<?php include('pagelinks.php'); ?>
	
	<?php
	
	foreach($pagination['collection'] as $model)
	{
		include($pagination['snap']);
	}
	
	?>
	
	<?php include('pagelinks.php'); ?>
	
<?php } ?>
