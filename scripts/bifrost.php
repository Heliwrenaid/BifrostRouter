<?php
function createDirectories($appDir) {
	global $separator;
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
        ROUTES_DIR . $separator . 'yaml' => 0755,
        ROUTES_DIR . $separator . 'php' => 0755,
        ROUTES_DIR . $separator . 'json' => 0755
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

if (DIRECTORY_SEPARATOR == '/') {
	$separator = '/';
} else {
	$separator = "\\\\";
}

$dir = 'vendor' . $separator . 'heliwrenaid' . $separator . 'bifrost-router';
$suffix = $dir . $separator . 'scripts';

$cmd = $argv[1];

preg_match('~^(.*)' . $suffix . '~', __DIR__, $matches);

if (!isset($matches[0]) || empty($matches)) {
    throw new Exception('Can\'t get app directory');
}

$appDir = $matches[1];
$packageDir = str_replace('\\\\', '\\', $appDir . $dir . $separator);

if ($separator == '\\\\') {
	$separator = "\\";
}

if ($cmd === 'install') {
    createDirectories($appDir);
    file_put_contents($packageDir . 'src' . $separator . 'core' . $separator . 'functions.php',
        '<?php function loadConfig(){ require \'' . $appDir . $separator . 'config.php\';}');

} elseif ($cmd === 'build') {
    require $packageDir . 'scripts' . $separator . 'build.php';
    build($appDir);
} elseif ($cmd === 'script-debug') {
    echo "Package directory: $packageDir\n";
    echo "App directory: $appDir\n";
} else {
	throw new Exception('Unknown command');
}
