<?php
namespace BifrostRouter;

class SpeedRoute extends Route {
    public function __construct($routeRegexs, $controller, $options = null) {
        $this->urls = $routeRegexs;
        $this->options = $options;
        $this->controller = $controller;
    }
}