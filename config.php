<?php
if (!defined('ROUTES_DIR')) {
	$dir = str_replace('\\', '/', __DIR__);
    define('CONTROLLERS_DIR', $dir . '/controllers/');
    define('VIEWS_DIR', $dir . '/views/');
    define('ROUTES_DIR', $dir . '/routes/');
    define('CACHE_DIR', $dir . '/cache/');
    define('SOURCE_DIR', $dir . '/src/');
    define('CONFIG_DIR', $dir . '/src/core/config/');
    define('SPEED_MODE_ROUTES_DIR', CONFIG_DIR . 'routes/');
    define('SCRIPTS_DIR', $dir . '/scripts/');
}
