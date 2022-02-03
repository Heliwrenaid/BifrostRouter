<?php
namespace BifrostRouter;
use Exception;
loadConfig();

/* OPTIONS:
'methods' => ['GET']] : POST,PUT etc
['lang' => 'en'] : wymaganie konkretnego jezyka
['matchQuery' = true] (default=false) : oproćz URL sprawdzaj też podane argumenty ('queries')
['filterPost' = true] (default=false) : filtruje całą tablice $_POST
*/
class Route{
    protected $name;
    protected $urls = [];
    protected $controller;
    protected $options = [];      // ['methods' => ['GET'], 'lang' => 'en', 'match_query' = false, 'verify_honey_pot' = false]

    public function __construct($name , $routeRegexs, $controller = null, $options = null){
        $this->name = $name;

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


        if (!isset($options['render'])) {
            $this->controller = $controller;

            $controllerClass = substr($this->controller, 0, strpos($this->controller, '::'));
            $method = substr($this->controller, strpos($this->controller, '::') + 2);

            if (!class_exists($controllerClass)) {
                throw new Exception('Cannot find controller: ' . $controllerClass);
            } else if (!method_exists($controllerClass, $method)) {
                throw new Exception('Cannot find method in controller: ' . $this->controller);
            }
        }

    }

#   special getters & setters
    public function getOption($name){
        return (isset($this->options[$name])) ? $this->options[$name] : null;
    }
   

#   getters & setters
    public function getName(){
        return $this->name;
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