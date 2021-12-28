<?php
require 'src/core/BifrostRouter.php';

function saveConfigFile($routerConf){
    $arr = array();
    foreach($routerConf->getRoutes() as $route){
        $arr[$route->getName()] = array('url' => $route->getUrls(), 'controller' => $route->getController());
    }
    file_put_contents('src/core/config/routes.json', json_encode($arr));
}
$router = new BifrostRouter(DEVELOPMENT_MODE);

saveConfigFile($router->getRouterConfig());
$router->saveConfigToCache($router->getRouterConfig());