<?php
namespace BifrostRouter;
use Exception;
use Symfony\Component\Yaml\Yaml;
loadConfig();

class RouterConfiguration{
    private $routeArr = [];
    private $optionsArr = [];
    private $page404;

    public function __construct($mode, $optionsArr = null){
        if ($optionsArr != null) {
            $this->optionsArr = $optionsArr;
        } else {
            //default options
            $this->optionsArr = array('routeOnceTime' => true,
                                        'rederictFromTrailingSlash' => false,
                                        'filterPost' => false
            );
        }
        switch ($mode) {
            case DEVELOPMENT_MODE:
            case CACHE_MODE: {
                $this->readRoutesFromUser();
                $this->areControllersNamesUnique();
                $this->areRoutesUrlsUnique();
            }
                break;
            case SPEED_MODE: {
                $file = SPEED_MODE_ROUTES_DIR . 'routes.json';
                if(!file_exists($file)) {
                    throw new Exception('Router configuration was not found. Run "php build.php".');
                }
                $this->readConfigForSpeedMode($file);
            }
                break;
            default: throw new Exception('RouterConfiguration: unknown mode');
        }
    }

    public function readConfigFromJson($file){
        $data = json_decode(file_get_contents($file), true);
        if (empty($data)) {
            throw new Exception('Error when parse routes in file: ' . $file. '. File is empty or has syntax error');
        }
        $this->readConfigFromArray($data);
    }

    public function readConfigFromYaml($file){
        $data = Yaml::parseFile($file);
        if (empty($data)) {
            throw new Exception('Error when parse routes in file: ' . $file. '. File is empty or has syntax error');
        }
        $this->readConfigFromArray($data);
    }

    public function readConfigFromPHP($file){
        $routes = require $file;
        if (empty($routes)) {
            throw new Exception('Error when parse routes in file: ' . $file. '. File is empty or has syntax error');
        }
        $this->readConfigFromArray($routes);
    }

    public function readConfigFromArray($data){
        if (isset($data['routes'])) {
            $this->readAndAddRoutes($data['routes']);
        }
        if (isset($data['groups'])) {
            foreach ($data['groups'] as $groupName => $groupData) {
                $this->readAndAddRoutes($groupData['routes'], $groupName, $groupData['prefix']);
            }
        }
    }

    private function readAndAddRoutes($routes, $groupName = null, $prefix = ''){
        if ($groupName !== null) {
            if (strpos($groupName, '-') !== false) {
                throw new Exception('Group name:' . $groupName . ' cannot consist of \'-\'');
            }

            $groupName .= '-';
        }

        foreach ($routes as $routeName => $routeData) {
            if (strpos($routeName, '-') !== false) {
                throw new Exception('Route name: ' . $groupName . $routeName . ' cannot consist of \'-\'');
            }

            if (isset($routeData['render'])) {
                $controllerName = null;
            } else {
                $controllerName = $this->generateController($routeData['controller'], rtrim($groupName, '-'));
            }
            $urls = explode(',', $routeData['path']);
            foreach ($urls as $key => $value) {
                $urls[$key] = '~^' . trim($prefix) . trim($value) . '$~';
            }

            // OPTIONS --------------------------------------------
            if (isset($routeData['methods'])) {
                $methods = explode(',', $routeData['methods']);

                if (empty($routeData['options'])){
                    $routeData['options'] = array();
                    $routeData['options']['methods'] = array();
                }

                foreach($methods as $method) {
                    array_push($routeData['options']['methods'], strtoupper(trim($method)));
                }
            }

            if (isset($routeData['render']) && $routeData['render'] == true) {
                if (empty($routeData['options'])){
                    $routeData['options'] = array();
                    $routeData['options']['render'] = array();
                }
                $routeData['options']['render'] = $routeData['render'];
            }

            if (isset($routeData['context'])) {
                if (empty($routeData['options'])){
                    $routeData['options'] = array();
                    $routeData['options']['context'] = array();
                }
                $routeData['options']['context'] = $routeData['context'];
            }
            // ----------------------------------------------------


            if (!isset($routeData['options'])) {
                array_push($this->routeArr, new Route($groupName . $routeName, $urls, $controllerName));
            } else {
                array_push($this->routeArr, new Route($groupName . $routeName, $urls, $controllerName, $routeData['options']));
            }  
        }   
    }

    public function readRoutesFromUser(){
        if (file_exists(ROUTES_DIR . 'yaml')) {
            $files = array_diff(scandir(ROUTES_DIR . 'yaml'), array('.', '..'));

            foreach($files as $file){
                $this->readConfigFromYaml(ROUTES_DIR . 'yaml' . DIRECTORY_SEPARATOR . $file);
            }
        }

        if (file_exists(ROUTES_DIR . 'json')) {
            $files = array_diff(scandir(ROUTES_DIR . 'json'), array('.', '..'));
            
            foreach($files as $file){
                $this->readConfigFromJson(ROUTES_DIR . 'json' . DIRECTORY_SEPARATOR . $file);
            }
        }

        if (file_exists(ROUTES_DIR . 'php')) {
            $files = array_diff(scandir(ROUTES_DIR . 'php'), array('.', '..'));
            
            foreach($files as $file){
                $this->readConfigFromPHP(ROUTES_DIR . 'php' . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    public function readConfigForSpeedMode($file){
        $data = json_decode(file_get_contents($file), true);
        foreach ($data as $controllerPath => $routeData) {
            if (isset($routeData['options'])) {
                array_push($this->routeArr, new SpeedRoute($routeData['name'], $routeData['url'], $controllerPath, $routeData['options']));
            } else {
                array_push($this->routeArr, new SpeedRoute($routeData['name'], $routeData['url'], $controllerPath,[]));
            }
        }
    }

    public function areControllersNamesUnique(){
        $routesNames = array();
        foreach ($this->routeArr as $route) {
            if ($route->getOption('render') !== true) {
                if (in_array($route->getController(), $routesNames)) {
                    throw new Exception('Controller name is not unique: ' . $route->getController());
                } else {
                    array_push($routesNames, $route->getController());
                }
            }
        }
    }

    public function areRoutesUrlsUnique(){
        $routesUrls = array();
        foreach ($this->routeArr as $route) {
            foreach ($route->getUrls() as $url) {
                if (in_array($url, $routesUrls)) {
                    throw new Exception('Url is not unique: ' . $url . ' in ' . $route->getName());
                } else {
                    array_push($routesUrls, $url);
                }
            }
        }
    }

    public function generateController($controllerName, $groupName = ''){
        if (strpos($controllerName, '-') !== false) {
            throw new Exception('Controller name can\'t consist "-" : ' . $controllerName);
        }

        $controllerName = ltrim($controllerName, '/');
        $controllerName = ltrim($controllerName, '\\');

        if (strpos($controllerName, '::') === false) {
            $controllerName .= '::run';
        }

        if (empty($groupName)) {
            return 'App\Controller\\' . $controllerName;
        } else {
            return 'App\Controller\\' . $groupName . '\\' . $controllerName;
        }
    }

    public function display() {
        foreach ($this->routeArr as $route) {
            echo 'ROUTE NAME: ' . $route->getName() . '</br>';
            echo "&nbsp;&nbsp;&nbsp;&nbsp;CONTROLLER: " . $route->getController() . '</br>';
            echo "&nbsp;&nbsp;&nbsp;&nbsp;URLs:</br>";
            foreach ($route->getUrls() as $url){
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $url . '</br>';
            }
            echo "&nbsp;&nbsp;&nbsp;&nbsp;OPTIONS:</br>";
            foreach ($route->getOptions() as $option){
                echo $option . '</br>';
            }
            echo '</br>-------------------------------------------------</br></br>';
        }
    }

    public function overrideOptions($optionsArr){
        $this->optionsArr = array_merge($this->optionsArr, $optionsArr);
    }

    public function getOption($name){
        return $this->optionsArr[$name];
    }

    public function getRoutes(){
        return $this->routeArr;
    }

    public function getOptions(){
        return $this->optionsArr;
    }

    public function get404Page(){
        return $this->page404;
    }

    public function set404Page($controller) {
        $this->page404 = $this->generateController($controller);
    }
}