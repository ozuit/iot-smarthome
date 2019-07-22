<?php

if (function_exists('tideways_xhprof_enable')) {
    tideways_xhprof_enable();
}

require dirname(__FILE__).'/index.php';

if (function_exists('tideways_xhprof_disable')) {
    $profileData = tideways_xhprof_disable();
    file_put_contents(storage_path('cache/profiler.json'), json_encode($profileData));
}

