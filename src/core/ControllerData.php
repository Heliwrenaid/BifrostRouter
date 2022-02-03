<?php
namespace BifrostRouter;
class ControllerData {
    private $scriptName;
    private $data;
    private $routeName;

    public function __construct($scriptName, $data){
        $this->scriptName = $scriptName;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;;
    }

    public function getRouteName() {
        return $this->routeName;
    }

    public function setRouteName($routeName) {
        $this->routeName = $routeName;
    }

    public function getScriptName() {
        return $this->scriptName;
    }
}