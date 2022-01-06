<?php

use Controller as GlobalController;

class Controller extends BifrostRouter\BaseController {
    public static function run($request) {
        echo 'echo from controller3';
        return new BifrostRouter\ControllerData ("example_script2", array('key1' => 'value1', 'key2' => 2));
    }
}
