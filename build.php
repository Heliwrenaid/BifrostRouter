<?php

require 'vendor/autoload.php';
loadConfig();
function saveConfigFile($routerConf){
    $arr = array();
    foreach($routerConf->getRoutes() as $route){

        $urls = $route->getUrls();
        if (!is_array($urls)){
            $urls = array($urls);
        }

        if (empty($route->getOptions())) {
            $arr[$route->getController()] = array('url' => $urls);
        } else {
            $arr[$route->getController()] = array('url' => $urls, 'options' => $route->getOptions());
        }

    }
    file_put_contents(SPEED_MODE_ROUTES_DIR . 'routes.json', json_encode($arr));
}
$router = new BifrostRouter\BifrostRouter(DEVELOPMENT_MODE);

saveConfigFile($router->getRouterConfig());
$router->saveConfigToCache($router->getRouterConfig());