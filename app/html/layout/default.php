<!doctype html>
<html lang="<?= $picara_lang ?>">
  <head>
    <meta charset="utf-8">
    <base href="<?= $base_href ?>" />
    <title><?= $meta_title ?></title>
    <meta name="keywords" content="<?= $meta_keywords ?>" />
    <meta name="description" content="<?= $meta_description ?>" />
    <meta http-equiv=”content-language” content=”<?= $picara_lang ?>”/>
    <? if(!LANG_IN_URLS): ?>
    <link rel="canonical" href="<?= $canonical ?>" />
    <? endif; ?>
    <? require(MOD . 'hreflang.php'); ?>
    <? if($noindex): ?>
    <meta name="robots" content="noindex" />
    <? endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <!-- ==== Nav ==== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
        <a class="navbar-brand" href="index/index">
            <? if(TITLE == DEFAULT_TITLE): ?>
                Picara<b>PHP</b>&nbsp;
            <? else: ?>
                <b><?= TITLE ?></b>
            <? endif; ?>
            <? if($link['controller'] == 'admin'): ?><?= _('Administration') ?><? endif; ?>
        </a>
        
        <? if(TITLE == DEFAULT_TITLE): ?>
            <ul class="navbar-nav ml-1">
                <li class="nav-item active">
                    <span class="navbar-text"><?= _('A Rapid PHP Development Framework') ?></a>
                </li>
            </ul>
        <? endif; ?>
        
        <? if(LANG_IN_URLS && is_array($picara_lang_change) && count($picara_lang_change) > 1): ?>
        <button class="navbar-toggler" type="button" 
                data-toggle="collapse" data-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- ==== Navbar ==== --> 
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <? require(MOD . 'l18n_navlinks.php'); ?>
            </ul>
        </div>
        <!-- ==== Navbar end ==== -->
        <? endif; ?>
    </nav>
    <!-- ==== End Nav ==== -->

    <!-- ==== Render View ==== -->
    <div id="wrap" role="main" class="container-fluid pl-5 pr-5">
            <? require(ACTION . "renderview.php"); ?>
    </div>
    <!-- ==== Render View ==== -->

    <!-- ==== Footer ==== -->
    <footer class="footer page-footer font-small mt-5 bg-light">
        <div class="footer-copyright text-center py-3">
            &copy; 2008-<?= date('Y') ?> Arturo Lopez Perez. <?= _('Distributed under MIT License.') ?>&nbsp;&nbsp; 
            <a href="pages/view/terms-and-conditions" title="Terms and Conditions"><?= _('Terms and Conditions') ?></a>&nbsp;&nbsp;
            <a href="pages/view/privacy-policy" title="Privacy Policy"><?= _('Privacy Policy') ?></a>
        </div>
    </footer>
    <!-- ==== Footer ==== -->
    
    <!-- ==== Optional JS ===== -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- ==== JS ==== -->
  </body>
</html>
