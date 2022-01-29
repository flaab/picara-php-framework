<div class="jumbotron mb-5" style="text-align: center;">
    <h1 class="display-4">Logs</h1>
    <p class="lead">This section allows you to examine the latest entries on the application logs. The default log is called <strong><?= DEFAULT_LOG ?></strong>. The current application environment is 
    <? if(ENVIRONMENT == 'production'): ?>
        <span class="badge badge-danger"><?= strtoupper(ENVIRONMENT) ?></span>, which means that only error messages are stored.
    <? elseif(ENVIRONMENT == 'development'): ?>
        <span class="badge badge-warning"><?= strtoupper(ENVIRONMENT) ?></span>, which means that warnings and errors are stored.
    <? else: ?>
        <span class="badge badge-secondary"><?= strtoupper(ENVIRONMENT) ?></span>, which means that warnings, errors and messages are stored.
    <? endif; ?> To create a log called <strong>foo</strong>, run <strong>php picara/scripts create log foo</strong> in the terminal.</p>
</div>
<ul class="nav nav-tabs">
    <? foreach($logs as $lg): ?>
        <li class="nav-item">
            <a class="nav-link <? if($current_log == $lg): ?>active<? endif; ?>" 
            href="<?= $link['controller'] ?>/<?= $link['action'] ?>/<?= $lg ?>" title="<?= ucwords($lg); ?> log">
                <?= ucwords($lg) ?>
            </a>
        </li>
    <? endforeach; ?>
</ul>
<div class="container-fluid pt-3">                
    <? if(strlen($log_content) > 0): ?>
        <textarea class="form-control form-control-sm small" rows="25" readonly><?= $log_content ?></textarea> 
    <? else: ?>
        <p>The log file is empty.</p>
    <? endif; ?>
</div>
