<?php
    require 'BaseController.php';
    class Controller implements BaseController {
        public static function run($request) {
            echo 'echo from controller1';
        }
    }
    
