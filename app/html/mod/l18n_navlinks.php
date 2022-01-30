<!-- ==== Language Menu Start ==== -->
<? if(LANG_IN_URLS && is_array($picara_lang_change) && count($picara_lang_change) > 1): ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLang" role="button" 
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= strtoupper($picara_lang) ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownLang">
            <? foreach($picara_lang_change as $key => $data): ?>
                <? if($key != $picara_lang): ?>        
                    <a class="dropdown-item" href="<?= $data['link'] ?>" title="<?= $data['name'] ?>">
                        <?= strtoupper($key) ?> &nbsp; <?= $data['name']; ?>
                    </a>
                <? endif; ?>
            <? endforeach; ?>
        </div>
    </li>
<? endif; ?>
<!-- ==== Language Menu End ==== -->