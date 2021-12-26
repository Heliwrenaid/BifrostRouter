<?php

class RouterConfigurationJSON{
    private $routeArr = [];
    private $optionsArr = [];
    private $page404;

    public function __construct($optionsArr = null){
        if ($optionsArr != null) {
            $this->optionsArr = $optionsArr;
        } else {
            //default options
            $this->optionsArr = array('routeOnceTime' => true,
                                        'rederictFromTrailingSlash' => false,
                                        'filterPost' => false
            );
        }
        $this->readConfig();
    }

    public function readConfig(){
        $routes = json_decode(file_get_contents('routes/json/routes.json'), true);
        foreach($routes as $route){
            array_push($this->routeArr, new Route($route['url'], $route['controller']));
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