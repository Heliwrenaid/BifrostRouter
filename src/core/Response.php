<?php
require_once('vendor/autoload.php');

class Response{
    private $loader;
    private $twig;
    private $status = 200;

    public function __construct($templatesDir = null, $honeyPotName = false, $csrfTokenName = 'csrfToken'){
        $this->twigSetup($templatesDir);
        $this->honeyPotName = $honeyPotName;
        $this->csrfTokenName = $csrfTokenName;
    }

    private function twigSetup($templatesDir = null){
        if($templatesDir == null){
            $this->loader = new \Twig\Loader\FilesystemLoader('/');
        } else {
            $this->loader = new \Twig\Loader\FilesystemLoader($templatesDir);
        }
        $this->twig = new \Twig\Environment($this->loader,array(
            'autoescape' => false
        ));
    }

    public function setTemplatesDir($templatesDir){
        $this->twigSetup($templatesDir);
    }

    public function render($template, $data = null){
        if($data == null){
            $this->twig->display($template);
        } else {
            $this->twig->display($template, $data);
        }
    }
    public function getTwig(){
        return $this->twig;
    }

    // JSON handling

    public function getStatus(){
        return $this->status;
    }
    public function setStatus($status){
        $this->status = $status;
    }

    public function json($data = []){
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
}