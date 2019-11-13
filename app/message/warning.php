<? if(is_array($_SESSION['picara']['controller_warning'])): ?>
        <? if(count($_SESSION['picara']['controller_warning']) == 1): ?>
            <div class="alert alert-warning" role="alert">
                <?= $_SESSION['picara']['controller_warning'][0] ?>
            </div>
        <? else: ?>
            <div class="alert alert-warning mb-4" role="alert">
                <h4 class="alert-heading mb-3">Success</h4>
                <ul>
                    <? foreach($_SESSION['picara']['controller_warning'] as $message): ?>
                        <li><?= $message ?></li>
                    <? endforeach; ?>
                </ul>
                <? if(ENVIRONMENT == "testing"): ?>
                    <hr>
                    <p class="mb-0 small">Edit this alert at <?= MESSAGE . 'warning.php' ?></p>
                <? endif; ?>
            </div>
        <? endif; ?>
<? endif; ?>
