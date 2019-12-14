<? if(is_array($_SESSION['picara']['controller_flash'])): ?>
        <? if(count($_SESSION['picara']['controller_flash']) == 1): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['picara']['controller_flash'][0] ?>
            </div>
        <? else: ?>
            <div class="alert alert-success mb-4" role="alert">
                <h4 class="alert-heading mb-3">Success</h4>
                <ul>
                    <? foreach($_SESSION['picara']['controller_flash'] as $message): ?>
                        <li><?= $message ?></li>
                    <? endforeach; ?>
                </ul>
                <? if(ENVIRONMENT == "testing"): ?>
                    <hr>
                    <p class="mb-0 small">Edit this alert at <?= MESSAGE . 'flash.php' ?></p>
                <? endif; ?>
            </div>
        <? endif; ?>
<? endif; ?>
