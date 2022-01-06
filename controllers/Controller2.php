<?php
class Controller extends BifrostRouter\BaseController {
    public static function run($request) {
        echo 'echo from controller2';
        return "example_script";
    }
}
