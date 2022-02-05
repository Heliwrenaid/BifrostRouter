<?php
function build($appDir) {
    require $appDir . 'vendor/autoload.php';
    loadConfig();
    $router = new BifrostRouter\BifrostRouter(DEVELOPMENT_MODE, ['buildMode' => true]);

    saveConfigFile($router->getRouterConfig());
    $router->saveConfigToCache($router->getRouterConfig());
}

// other functions ---------------------------------------

function saveConfigFile($routerConf){
    $arr = array();
    foreach($routerConf->getRoutes() as $route){

        $urls = $route->getUrls();
        if (!is_array($urls)){
            $urls = array($urls);
        }

        if (empty($route->getOptions())) {
            $arr[$route->getName()] = array('controller' => $route->getController(), 'path' => $urls);
        } else {
            $arr[$route->getName()] = array('controller' => $route->getController(), 'path' => $urls, 'options' => $route->getOptions());
        }

        createController($route);

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
        
        foreach($arr as $routeName => $routeData) {
            foreach ($routeData['path'] as $url) {
                if (fwrite($fp, json_encode(['name' => $routeName, 'path' => $url]) . "\n") === FALSE) {
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

function createController($route){
    if ($route->getOption('render') != true) {
        $controller = $route->getController();
        $controllerClass = substr($controller, 0, strpos($controller, '::'));
        $method = substr($controller, strpos($controller, '::') + 2);

        if (!class_exists($controllerClass)) {
            $controllerDir = CONTROLLERS_DIR;
            if(substr($controllerDir, -1, 1) === '/') {
                $controllerDir = substr($controllerDir, 0, -1);
            }
            if(substr($controllerDir, -1, 1) === '\\') {
                $controllerDir = substr($controllerDir, 0, -1);
            }

            $controllerFile = str_replace('App\\Controller\\', '', $controllerClass);
            $controllerFile = str_replace('\\', DIRECTORY_SEPARATOR, $controllerFile);

            $controllerName = pathinfo($controllerFile, PATHINFO_FILENAME);

            $controllerPath = $controllerDir . DIRECTORY_SEPARATOR . $controllerFile . '.php';
            $controllerPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerPath);
            $controllerPath = str_replace('/', DIRECTORY_SEPARATOR, $controllerPath);
            
            $controllerNameSpace = str_replace("\\$controllerName", '', $controllerClass);


$controllerCode = "<?php
namespace $controllerNameSpace;
class $controllerName extends \BifrostRouter\BaseController {
    public static function $method(\$request) {
        echo 'Output from: $controllerClass::$method()';
    }
}";

            file_put_contents($controllerPath, $controllerCode);
            echo "Created controller class: $controllerClass\n";
        } else if (!method_exists($controllerClass, $method)) {
            throw new Exception("Cannot find method : $method in controller: $controller");
        }
    }
}