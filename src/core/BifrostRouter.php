<?php

namespace BifrostRouter;

use BifrostRouter\Request;
use BifrostRouter\RouterConfiguration;
use Exception;

require('modes.php');
loadConfig();

class BifrostRouter{
    private $routerConfig;
    private $request;

    public function __construct($mode = null){
        if ($mode === null){
            $mode = DEVELOPMENT_MODE;
        }
        if ($mode === CACHE_MODE) {
            $this->routerConfig = $this->getConfigFromCache();
            if($this->routerConfig == null){
                $this->routerConfig = new RouterConfiguration($mode);
                $this->saveConfigToCache($this->routerConfig);
            }
        } else {
            $this->routerConfig = new RouterConfiguration($mode);
        }
        
    }

    public function getConfigFromCache(){
        $file = CACHE_DIR . 'router.cache';
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));			
        } else {
            return null;
        }
    }

    public function saveConfigToCache($obj) {
        $file = CACHE_DIR . 'router.cache';
		file_put_contents($file, serialize($obj));
	}

    private function parse_route_opt($route){

        $routeMethod = $route->getOption('method');
        if(isset($routeMethod)){
            if($this->request->getMethod() != $routeMethod){
                return false;
            }
        }

        $routeLang = $route->getOption('lang');
        if(isset($routeLang)){
            if($routeLang != $this->request->getLanguage()){
                return false;
            }
        }

       return true;
    }

    private function getUrlForRouting($route){
        $url = $this->request->getUrl();
        if($this->routerConfig->getOption('rederictFromTrailingSlash')){
            if(substr($url, -1) == '/'){
                $url = substr($url, 0, -1);
                header("Location:$url");
            }
        }
        if($route->getOption('matchQuery') && !empty($this->request->getQuery())){
            return $url . '?' . $this->request->getQuery();
        }else {
            return $url;
        }
        
    }

    public function start(){
        $this->request = new Request();

        foreach($this->routerConfig->getRoutes() as $route){
            $url = $this->getUrlForRouting($route);

            foreach($route->getUrls() as $route_url){
                if(preg_match($route_url, $url, $matches)){
                    if ($this->parse_route_opt($route)) {

                        if($this->routerConfig->getOption('filterPost') || $route->getOption('filterPost')){
                            $this->filterPost();
                        }

                        $this->request->vars = filter_var_array(array_slice($matches, 1, count($matches) - 1),FILTER_SANITIZE_SPECIAL_CHARS);

                        $this->handle($route->getController(), $route->getName());

                        $isMatched = true;
                        if (!empty($this->routerConfig->getOption('routeOnceTime'))){ return;}
                        break;
                    }
                }
            }
        }
        if(!isset($isMatched)){
            $this->load404Page();
            exit;
        }
    }

    private function load404Page(){
        header('HTTP/1.1 404 Not Found');
        if(empty($this->routerConfig->get404Page())){
            Page404::run(new Request);
        } else {
            $this->handle($this->routerConfig->get404Page(), 'page-404');
        }
       
    }

    public function handle($controller, $routeName) {
        require $controller;
        $var = \Controller::run($this->request);

        if (empty($var)){
            return;
        } else if (is_int($var)) {
            if(in_array(
                $var, 
                array(100, 101, 110, 111, 200, 201, 202, 203, 204, 205, 206,
                300, 301, 302, 303, 304, 305, 306, 307, 310,
                400, 401, 402, 403, 404, 405, 406, 407, 408, 409,
                410, 411, 412, 413, 414, 415, 416, 417, 418, 421,
                422, 423, 424, 429, 431, 451, 
                500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511
                )
            )){
            http_response_code($var);
            } else {
                throw new Exception('HTTP code ' . $var . ' is invalid in controller: ' . $controller);
            }
        } else if (is_string($var)){
            ScriptSandbox::runScript($var);
        } else if (is_object($var) && get_class($var) == 'BifrostRouter\ControllerData') {
           ScriptSandbox::runControllerData($var, $routeName);
        } else {
            throw new Exception('Value returned by controller: ' . $controller . ' is invalid');
        }
    }

#   getters & setters
    public function set404Page($controller){
        $this->routerConfig->set404Page($controller);
    }
    public function filterPost(){
        foreach($_POST as $key => $value){
            $_POST[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }
    }

    public function getRouterConfig(){
        return $this->routerConfig;
    }
}