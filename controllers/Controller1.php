<?php
class Controller extends BifrostRouter\BaseController {
    public static function run($request) {
        echo 'echo from controller1';
        return 405;
    }
}
