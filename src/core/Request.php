<?php

namespace BifrostRouter;

class Request {
    private $url = [];
    private $method;

    private $defaultLang = 'en';
    private $lang;
    private $acceptLangs = [];
    public $vars = [];

    public function __construct(){
        $this->url = parse_url($_SERVER['REQUEST_URI']);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $this->acceptLangs = array($this->defaultLang);
    }

    public function getJson($json, $toArray = null){
        if(isset($json)){
            if($toArray){
                $data = json_decode($json, true);
                foreach($data as $key => $value){
                    $data[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
                }
            } else {
                $data = json_decode($json);
                foreach($data as $key => $value){
                    $data->$key = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
                }
            }
            return $data;
        } else {
            return [];
        }
    }
    public function post($key){
        if(isset($_POST[$key])){
            return htmlspecialchars($_POST[$key], ENT_QUOTES | ENT_HTML401, 'UTF-8');
        } else {
            return null;
        }
    }
    public function get($key){
        if(isset($_GET[$key])){
            return htmlspecialchars($_POST[$key], ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }else{
            return null;
        }

    }
    
    public function filterPost(){
        foreach($_POST as $key => $value){
            $_POST[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }
    }

    public function isFileSent($filename){
        if(empty($_FILES[$filename]['tmp_name'])){
          return false;
        }else{
          return true;
        }
    }

    public function getUrl(){
        return (isset($this->url['path'])) ? $this->url['path'] : '';
    }
    public function getQuery(){
        return (isset($this->url['query'])) ? $this->url['query'] : '';
    }
    public function getHost(){
        return (isset($this->url['host'])) ? $this->url['host'] : '';
    }
    public function getFragment(){
        return (isset($this->url['fragment'])) ? $this->url['fragment'] : '';
    }

    public function getMethod(){
        return $this->method;
    }

    public function getContentType(){
        return $this->contentType;
    }

    public function getLanguage(){ 
        return in_array($this->lang, $this->acceptLangs) ? $this->lang : $this->defaultLang;
    }

    public function setDefaultLanguage($lang){
        $this->defaultLang = $lang;
    }

    public function setAcceptedLanguages($langs_arr){
        $this->acceptLangs = $langs_arr;
    }
}