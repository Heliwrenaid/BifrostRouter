<?php

class RouterConfiguration{
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
    }

    public function addRoute($route){
        if (array_key_exists($route->getName(), $this->routeArr)) {
            throw new Exception('Can\'t add route: array key already exists');
        } else {
            $this->routeArr[$route->getName()] = (object)$route;
        }
    }

    public function route($routeRegexs, $controller, $options = null){
        $this->addRoute(new Route($routeRegexs, $controller, $options));
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