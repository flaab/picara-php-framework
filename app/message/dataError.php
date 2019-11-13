<? if(is_array($_SESSION['picara']['controller_dataerrors'])): ?>
        <? if(count($_SESSION['picara']['controller_dataerrors']) == 1): ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['picara']['controller_dataerrors'][0] ?>
            </div>
        <? else: ?>
            <div class="alert alert-danger mb-4" role="alert">
                <h4 class="alert-heading mb-3">Oups...</h4>
                <ul>
                    <? foreach($_SESSION['picara']['controller_dataerrors'] as $message): ?>
                        <li><?= $message ?></li>
                    <? endforeach; ?>
                </ul>
                <? if(ENVIRONMENT == "testing"): ?>
                    <hr>
                    <p class="mb-0 small">Edit this alert at <?= MESSAGE .'dataError.php' ?></p>
                <? endif; ?>
            </div>
        <? endif; ?>
<? endif; ?>
