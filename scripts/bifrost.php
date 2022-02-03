<?php
function createDirectories($appDir) {
    require $appDir . 'config.php';
    $oldMask = umask(0);
    $directories = [
        SOURCE_DIR => 0755,
        CONTROLLERS_DIR => 0755,
        VIEWS_DIR => 0755,
        ROUTES_DIR => 0755,
        SCRIPTS_DIR => 0755,
        CACHE_DIR => 0777,
        CONFIG_DIR => 0777,
        SPEED_MODE_ROUTES_DIR => 0777,
        ROUTES_DIR . '/yaml' => 0755,
        ROUTES_DIR . '/php' => 0755,
        ROUTES_DIR . '/json' => 0755
    ];

    foreach ($directories as $directory => $mode)
    if (!file_exists($directory)) {
        mkdir($directory, $mode, true);
    }
    umask($oldMask);
}

// -----------------------------

if (!isset($argv[1])) {
    echo "No argument passed\n";
    exit;
}

$dir = 'vendor/heliwrenaid/bifrost-router';
$suffix = $dir . '/scripts';

$cmd = $argv[1];

preg_match('~^(.*)' . $suffix . '~', __DIR__, $matches);

if (!isset($matches[0]) || empty($matches)) {
    throw new Exception('Can\'t get app directory');
}

$appDir = $matches[1];
$packageDir = $appDir . $dir . '/';

if ($cmd === 'install') {
    createDirectories($appDir);
    file_put_contents($packageDir . 'src/core/functions.php',
        '<?php function loadConfig(){ require \'' . $appDir . '/config.php\';}');

} elseif ($cmd === 'build') {
    require $packageDir . 'scripts/build.php';
    build($appDir);
} elseif ($cmd === 'script-debug') {
    echo "Package directory: $packageDir\n";
    echo "App directory: $appDir\n";
}
