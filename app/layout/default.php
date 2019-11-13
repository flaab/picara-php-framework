<!doctype html>
<html lang="en">
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
        <a class="navbar-brand" href="index/index">
            <? if(TITLE == DEFAULT_TITLE): ?>
                Picara<b>PHP</b>&nbsp;
            <? else: ?>
                <b><?= TITLE ?></b>
            <? endif; ?>
            <? if($link['controller'] == 'admin'): ?>Administration<? endif; ?>
        </a>
        
        <? if(TITLE == DEFAULT_TITLE): ?>
            <ul class="navbar-nav ml-1">
                <li class="nav-item active">
                    <span class="navbar-text" title="Administration">A Rapid PHP Development Framework</a>
                </li>
            </ul>
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
            &copy; 2008-<?= date('Y') ?> Arturo Lopez Perez. Distributed under MIT License.&nbsp;&nbsp; 
            <a href="pages/view/terms-and-conditions" title="Terms and Conditions">Terms and Conditions</a>&nbsp;&nbsp;
            <a href="pages/view/privacy-policy" title="Privacy Policy">Privacy Policy</a>
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
