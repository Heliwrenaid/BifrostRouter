<?php
function createComposerJson($dir) {
    $data = array();
    $data['autoload'] = array('psr-4' => array('App\\' => 'src'));
    file_put_contents($dir . '/composer.json', json_encode($data, JSON_PRETTY_PRINT));
}

if (!is_writable(__DIR__ .'/src/core/functions.php')){
    echo "Source directory is not writable ... aborted\n"; exit;
}

if (isset($argv[1])) {
    $dir = $argv[1];
} else {
    $dir = readline('Enter path to app directory: ');
    if (empty($dir)) {
        $dir = $_SERVER['DOCUMENT_ROOT'];
    }
}

if (substr($dir, -1) == '/') {
    $dir = substr($dir, 0 , -1);
}

file_put_contents(__DIR__ . '/src/core/functions.php',
        '<?php function loadConfig(){ require \'' . $dir . '/config.php\';}');

if (!copy(__DIR__ . '/config.php', $dir . '/config.php')) {
    echo "Failed to copy config file...\n"; exit;
}

if(file_exists($dir . '/composer.json')) {
    $data = json_decode(file_get_contents($dir . '/composer.json'), true);
    if (!isset($data['autoload'])){
        $data['autoload'] = array();
        $data['autoload'] = array('psr-4' => array('App\\' => 'src'));
    } elseif(isset($data['autoload']['psr-4'])) {
        array_merge($data['autoload']['psr-4'], array('App\\' => 'src'));
    } else {
        array_merge($data['autoload'], array('psr-4' => array('App\\' => 'src')));
    }
    file_put_contents($dir . '/composer.json', json_encode($data, JSON_PRETTY_PRINT));
} else {
    createComposerJson($dir);
}

echo "\nstatus: success\n\n";