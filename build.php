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

        $name = $route->getName();

        if (empty($route->getOptions())) {
            $arr[$route->getController()] = array('name' => $name, 'url' => $urls);
        } else {
            $arr[$route->getController()] = array('name' => $name, 'url' => $urls, 'options' => $route->getOptions());
        }

    }
    file_put_contents(SPEED_MODE_ROUTES_DIR . 'routes.json', json_encode($arr));

    // Response rederict--------------------
    $filename = \BifrostRouter\Response::getUrlResolverPath();

    if(file_exists($filename)) {
        unlink($filename);
    }

    $fp =  fopen($filename, 'w');
    fclose($fp);

    if (is_writable($filename)) {
        if (!$fp = fopen($filename, 'a')) {
             echo "Cannot open file ($filename)";
             exit;
        }
        
        foreach($arr as $routeData) {
            foreach ($routeData['url'] as $url) {
                if (fwrite($fp, json_encode(['name' => $routeData['name'], 'path' => $url]) . "\n") === FALSE) {
                    echo "Cannot write to file ($filename)";
                    exit;
                }
            }
        }

        fclose($fp);
    
    } else {
        echo "The file $filename is not writable";
    }
}
$router = new BifrostRouter\BifrostRouter(DEVELOPMENT_MODE);

saveConfigFile($router->getRouterConfig());
$router->saveConfigToCache($router->getRouterConfig());