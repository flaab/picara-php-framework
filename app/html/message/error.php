<? if(is_array($_SESSION['picara']['controller_errors'])): ?>
        <? if(count($_SESSION['picara']['controller_errors']) == 1): ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['picara']['controller_errors'][0] ?>
            </div>
        <? else: ?>
            <div class="alert alert-danger mb-4" role="alert">
                <h4 class="alert-heading mb-3">Errors</h4>
                <ul>
                    <? foreach($_SESSION['picara']['controller_errors'] as $message): ?>
                        <li><?= $message ?></li>
                    <? endforeach; ?>
                </ul>
                <? if(ENVIRONMENT == "testing"): ?>
                    <hr>
                    <p class="mb-0 small">Edit this alert at <?= MESSAGE . 'error.php' ?></p>
                <? endif; ?>
            </div>
        <? endif; ?>
<? endif; ?>
