<?php
if (!defined('ROUTES_DIR')) {
    $subDir = '/';
    $dir = str_replace('\\', '/', __DIR__) . $subDir;
    define('SOURCE_DIR', $dir . 'src/');
    define('CONTROLLERS_DIR', SOURCE_DIR . 'Controller/');
    define('VIEWS_DIR', SOURCE_DIR . 'views/');
    define('ROUTES_DIR', SOURCE_DIR . 'routes/');
    define('CACHE_DIR', $dir . 'cache/');
    define('CONFIG_DIR', CACHE_DIR . 'config/');
    define('SPEED_MODE_ROUTES_DIR', CONFIG_DIR . 'routes/');
    define('SCRIPTS_DIR', SOURCE_DIR . 'scripts/');
}
