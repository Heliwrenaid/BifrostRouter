#!/bin/bash
number=100
file='routes.yaml'
touch $file
for i in `seq 1 $number`; do
echo "route${i}:
    url: ~home${i}~
    controller: 'controllers/Controller${i}.php'" >> $file

done

controllersDir='controllers'
for i in `seq 1 $number`; do
    controller="${controllersDir}/Controller${i}.php"
    echo "<?php
    require 'BaseController.php';
    class Controller implements BaseController {
        public static function run(\$request) {
            echo 'echo from controller${i}';
        }
    }
    " > $controller
done