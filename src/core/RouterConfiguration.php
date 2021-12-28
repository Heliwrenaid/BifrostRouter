<?php

require 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

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
            case CACHE_MODE: $this->readRoutesFromUser();
                break;
            case SPEED_MODE: {
                $file = __DIR__ . '/config/routes.json';
                if(!file_exists($file)) {
                    throw new Exception('Router configuration was not found. Run "php build.php".');
                }
                $this->readConfigFromJson($file);
            }
                break;
            default: throw new Exception('RouterConfiguration: unknown mode');
        }
    }

    public function readConfigFromJson($file){
        $routes = json_decode(file_get_contents($file), true);
        foreach($routes as $route){
            array_push($this->routeArr, new Route($route['url'], $route['controller']));
        }
    }

    public function readConfigFromYaml($file){
        $routes = Yaml::parseFile($file);
        foreach($routes as $route){
            array_push($this->routeArr, new Route($route['url'], $route['controller']));
        }
    }

    public function readConfigFromPHP($file){
        $routes = require $file;
        foreach($routes as $route){
            array_push($this->routeArr, new Route($route['url'], $route['controller']));
        }
    }

    public function readRoutesFromUser(){
        $files = array_diff(scandir('routes/yaml'), array('.', '..'));

        foreach($files as $file){
            $this->readConfigFromYaml('routes/yaml/' . $file);
        }

        $files = array_diff(scandir('routes/json'), array('.', '..'));
        
        foreach($files as $file){
            $this->readConfigFromJson('routes/json/' . $file);
        }

        $files = array_diff(scandir('routes/php'), array('.', '..'));
        
        foreach($files as $file){
            $this->readConfigFromPHP('routes/php/' . $file);
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


}