<?php
session_start();
if (true){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require "src/core/BifrostRouter.php";

// // YAML with cache
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     $router = SimpleCache::get();
//     if ($router == null) {
//         $router = new BifrostRouter(new RouterConfigurationYAML());
//         SimpleCache::set($router);
//     }
//     unset($ruter);
// }
// $end = microtime(true);


// // YAML: without cache -> SLOW
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     $router = new BifrostRouter(new RouterConfigurationYAML());
//     unset($router);
// }
// $end = microtime(true);

// // JSON: without cache
// require "src/core/RouterConfiguration/RouterConfigurationJSON.php";
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     $router = new BifrostRouter(new RouterConfigurationJSON());
//     unset($router);
// }
// $end = microtime(true);

// // //test YAML parser
// require 'vendor/autoload.php';
// use Symfony\Component\Yaml\Yaml;
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     $routes = Yaml::parseFile('routes/yaml/routes.yaml');
// }
// $end = microtime(true);

// //test JSON
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     $values = json_decode(file_get_contents('routes/json/routes.json'), true);
// }
// $end = microtime(true);

// //test PHP
// require "src/core/RouterConfiguration/RouterConfiguration.php";
// $start = microtime(true);
// for($i = 0; $i < 100; $i++) {
//     include 'routes/php/routes.php';
//     unset($routerConf);
// }
// $end = microtime(true);


//test modes
$start = microtime(true);
for($i = 0; $i < 10000; $i++) {
    $router = new BifrostRouter(SPEED_MODE);
    unset($router);
}
$end = microtime(true);

echo '</br>';
$time = $end - $start;
echo 'AVG: ' . $time/10000 . '</br>';

$router = new BifrostRouter(DEVELOPMENT_MODE);
$router->start();