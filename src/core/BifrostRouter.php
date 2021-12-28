<?php

require('Route.php');
require('Request.php');
require('RouterConfiguration.php');
require('modes.php');

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
        $file = 'cache/router.cache';
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));			
        } else {
            return null;
        }
    }

    public function saveConfigToCache($obj) {
        $file = 'cache/router.cache';
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

                        require $route->getController();
                        Controller::run($this->request);

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
        //header('HTTP/1.1 404 Not Found');
        $page = $this->page404;
        if(isset($page)){
            if(is_string($page)){
                require_once($page);
            } else {
                throw new Exception('404 page can\'t be loaded.');
            }
        }else {
            echo '<h2>ERROR 404: website was not found</h2>';
        }
    }

#   getters & setters
    public function setOptions($optionsArr){
        $this->optionsArr = $optionsArr;
    }
    public function set404Page($page){
        $this->page404 = $page;
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