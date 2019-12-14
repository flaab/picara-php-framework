<? if($pagination['last_page'] != 1): ?>
<nav aria-label="pagination links">
    <ul class="pagination justify-content-center">
        <li class="page-item">
	        <a class="page-link" href="<?= $pagination['base_link'] . $pagination['first_page'] ?>">First</a>
        </li>
	    <? if($pagination['page'] > $pagination['first_page']): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pagination['base_link'] . ($pagination['page']-1) ?>">&lt;</a>
            </li>
        <? endif; ?>
	    <? foreach($pagination['left_links'] as $page): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pagination['base_link'] . $page ?>"><?= $page ?></a>
            </li>
        <? endforeach; ?>
        <li class="page-item active">	    
            <span class="page-link">
                <?= $pagination['page'] ?>
                <span class="sr-only">(current)</span>
            </span>
        </li>
	    <? foreach($pagination['right_links'] as $page): ?>
            <li class="page-item">
	            <a class="page-link" href="<?= $pagination['base_link'] . $page ?>"><?= $page ?></a>
            </li>
        <? endforeach; ?>
	    <? if($pagination['page'] < $pagination['last_page']): ?>
            <li class="page-item">
	            <a class="page-link" href="<?= $pagination['base_link']. ($pagination['page']+1) ?>">></a>
            </li>
        <? endif; ?>	
        <li class="page-item">
	        <a class="page-link" href="<?= $pagination['base_link'] . $pagination['last_page'] ?>">Last</a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                <?= $pagination['first_result'] ?>-<?= $pagination['last_result'] ?> of <?= $pagination['total_results'] ?> results
            </a>
        </li>
    </ul>
</nav>
<? endif; ?>
