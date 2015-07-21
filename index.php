<?php

require 'vendor/autoload.php';

$moe        = moe\Base::instance();
$config_dir = 'app/config/';
$config_ext = '.ini';
foreach ([
    'system',
    'admin.routes',
    'user.routes',
    'database',
    'app',
] as $config)
    $moe->config($config_dir.$config.$config_ext);
$moe->run();
