<?php
    ob_start("ob_gzhandler");

    $time_start = microtime(true);

    require_once('includes/site.php');

    $site = new Site();

    if (isset($_GET['jsonMode']) || isset($_POST['jsonMode'])) {
        $site->outputJson();
    } else {
        $site->output();
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    if ((!isset($_GET['popup']) && !isset($_GET['jsonMode'])) && $GLOBALS['config']['site']['showstats']) {
        echo '<script type="text/javascript">$("#footer").html($("#footer").html() + "<br />';
        echo '<small><small>Processed in ' . round($time, 4) . ' seconds, ';
        echo (DB::getInstance()->numQueries) . ' DB queries; ';
        echo round((memory_get_usage()/1024), 2) . 'kb memory used.<br />';
        echo '</small></small>");';
        echo '</script>';
    }

    ob_end_flush();
?>
