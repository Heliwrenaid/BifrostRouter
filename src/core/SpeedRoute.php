<?php
namespace BifrostRouter;

class SpeedRoute extends Route {
    public function __construct($name, $routeRegexs, $controller, $options = null) {
        $this->name = $name;
        $this->urls = $routeRegexs;
        $this->options = $options;
        $this->controller = $controller;
    }
}