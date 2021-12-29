<?php

/* OPTIONS:
'method' => 'GET'] : POST,PUT etc
['lang' => 'en'] : wymaganie konkretnego jezyka
['matchQuery' = true] (default=false) : oproćz URL sprawdzaj też podane argumenty ('queries')
['filterPost' = true] (default=false) : filtruje całą tablice $_POST
*/
class Route{
    private $urls = [];
    private $controller;
    private $options = [];      // ['method' => 'GET', 'lang' => 'en', 'match_query' = false, 'verify_honey_pot' = false]

    public function __construct($routeRegexs, $controller, $options = null){

        if(is_array($routeRegexs)){
            $this->urls = $routeRegexs;
        }else {
            $this->urls = array($routeRegexs);
        }
        
        if ($options !== null){
            if(is_array($options)){
                $this->options = $options;
            }else{
                throw new Exception('Route options must be an array.');
            }
        }

        $this->controller = $controller;
        if (!file_exists($this->controller)) {
            throw new Exception('Controller file doesn\'t exists (filepath : ' . $this->controller .' )');
        }

    }

#   special getters & setters
    public function getOption($name){
        return (isset($this->options[$name])) ? $this->options[$name] : null;
    }
   

#   getters & setters
    public function getName(){
        return pathinfo($this->controller, PATHINFO_FILENAME);
    }
    public function getUrls(){
        return $this->urls;
    }
    public function getController(){
        return $this->controller;
    }
    public function getOptions(){
        return $this->options;
    }

    public function setUrls($urls){
        $this->urls = $urls;
    }
    public function setController($controller){
        $this->controller = $controller;
    }
    public function setOptions($options){
        $this->options = $options;
    }
}