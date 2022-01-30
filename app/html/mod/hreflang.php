<? if(LANG_SUPPORT && LANG_IN_URLS && is_array($picara_lang_change)): ?>
<? foreach($picara_lang_change as $key => $data): ?>
<? if($key == DEFAULT_LANG): ?>
<link rel="alternate" hreflang="x-default" href="<?= PICARA_BASE_HREF . implode("/", $request_array) ?>" />
<? endif; ?>
<link rel="alternate" hreflang="<?= $key ?>" href="<?= $data['link'] ?>" />
<? endforeach; ?>
<? endif; ?>