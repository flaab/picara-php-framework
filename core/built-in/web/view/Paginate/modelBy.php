<?php

/**
 * Built-in view for related models pagination
 */

?>

<h1><?= $valueName ?></h1>

<?php

if(isset($pagination['empty']))
{
	echo "<p>No results found.</p>";

} else {

?>

	<h3>Showing <b><?= $pagination['first_result'] ?></b> to <b><?= $pagination['last_result'] ?></b> from <b><?= $pagination['total_results'] ?></b> <?= $pagination['display'] ?></h3>
	
	<?php include('pagelinks.php'); ?>
	
	<?php
	
	foreach($pagination['collection'] as $model)
	{
		include($pagination['snap']);
	}
	
	?>
	
	<?php include('pagelinks.php'); ?>
	
<?php } ?>
