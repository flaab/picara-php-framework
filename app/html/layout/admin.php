<!doctype html>
<html lang="<?= $picara_lang ?>">
  <head>
    <meta charset="utf-8">
    <base href="<?= $base_href ?>" />
    <title><?= $meta_title ?></title>
    <meta name="keywords" content="<?= $meta_keywords ?>" />
    <meta name="description" content="<?= $meta_description ?>" />
    <link rel="canonical" href="<?= $canonical ?>" />
    <? if($noindex): ?>
    <meta name="robots" content="noindex" />
    <? endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <!-- ==== Nav ==== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
        <a class="navbar-brand" href="admin/welcome">
            <? if(TITLE == DEFAULT_TITLE): ?>
                Picara<b>PHP</b>
            <? else: ?>
                <b><?= TITLE ?></b>
            <? endif; ?>
            <?= _('Site Administration') ?>
        </a>
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
                <? if(Pi_session::check(ADMIN_SESSION)): ?>
                    <li class="navbar-text text-white mr-2"><?= _('Welcome') ?>, <?= Pi_session::read("name"); ?></li>
                    <!-- ==== Scaffold Start ==== -->
                    <? if(isset($other_scaffolds) && count($other_scaffolds) > 0): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownModels" role="button" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Scaffolds
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownModels">
                                <? $tconn = count($other_scaffolds); $itconn = 0;?>
                                <? foreach($other_scaffolds as $connection => $data): $itconn++; ?>
                                    <? foreach($data as $value): ?>
                                        <a class="dropdown-item" href="<?= $value['controller'] ?>"><?= $value['display']; ?></a>
                                    <? endforeach; ?>
                                    <? if($itconn < $tconn): ?><div class="dropdown-divider"></div><? endif; ?>
                                <? endforeach; ?>
                            </div>
                        </li>
                    <? endif; ?>
                    <!-- ==== Scaffold End ==== -->
                    <!-- ==== Custom admin menu start ==== -->
                    <? if(isset($custom_menu) && count($custom_menu) > 0): ?>
                        <? foreach($custom_menu as $name => $links): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?= $name ?>" role="button" 
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?= ucwords($name); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown<?= $name ?>">
                                    <? foreach($links as $l_link => $goto): ?>
                                        <a class="dropdown-item" href="<?= $goto ?>"><?= ucwords($l_link) ?></a>
                                    <? endforeach; ?>
                                </div>
                            </li>
                        <? endforeach; ?>
                    <? endif; ?>
                    <!-- ==== Custom admin menu end ==== -->
                    <li class="nav-item">
                        <a class="nav-link" href="admin/logout" title="<?= _('Logout') ?>"><?= _('Logout') ?></a>
                    </li>
                <? elseif($link['action'] != 'login'): ?> 
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login" title="<?= _('Login') ?>"><?= _('Login') ?></a>
                    </li>
                <? else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index" title="<?= _('Back to site') ?>"><?= _('Back to site') ?></a>
                    </li>
                <? endif; ?>
                <? require(MOD . 'l18n_navlinks.php'); ?>
            </ul>
        </div>
        <!-- ==== Navbar end ==== -->
    </nav>
    <!-- ==== End Nav ==== -->
    
    <!-- ==== Render View ==== -->
    <div class="container-fluid pr-5 pl-5" role="main">
        <? require(ACTION . "renderview.php"); ?>
    </div>
    <!-- ==== Render View ==== -->

    <!-- ==== Footer ==== -->
    <footer class="footer page-footer font-small mt-5 bg-light">
        <div class="footer-copyright text-center py-3">
            &copy; 2008-<?= date('Y') ?> Arturo Lopez Perez. <?= _('Distributed under MIT License.') ?> &nbsp;
            <? if($link['action'] != 'login'): ?>
                <? if(ENVIRONMENT == 'production'): ?>
                    <span class="badge badge-danger">
                <? elseif(ENVIRONMENT == 'development'): ?>
                    <span class="badge badge-warning">
                <? else: ?>
                    <span class="badge badge-secondary">
                <? endif; ?>
                <?= strtoupper(ENVIRONMENT) ?></span>
            <? endif; ?>
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
