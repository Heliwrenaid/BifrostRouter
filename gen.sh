#!/bin/bash
if [[ $1 == '' ]]; then
    echo 'ERROR: enter number of routes'
    exit
fi
number=$1


file='routes/yaml/routes.yaml'
echo '' > $file
for i in `seq 1 $number`; do
echo "route${i}:
    url: ~home${i}\$~
    controller: 'controllers/Controller${i}.php'" >> $file

done



file='routes/php/routes.php'
echo '<?php' > $file
echo 'return [' >> $file
for i in `seq 1 $number`; do
    echo "'route${i}' => ['url' => '~home${i}\$~', 'controller' => 'controllers/Controller${i}.php' ]," >> $file
done
echo '];' >> $file



file='routes/json/routes.json'
echo '{' > $file
let "num=number-1";
for i in `seq 1 $num`; do
    echo "\"controllers_Controller${i}.php\":{" >> $file
    echo "\"url\":[\"~home${i}$~\"]," >> $file
    echo "\"controller\":\"controllers\\/Controller${i}.php\"" >> $file
    echo '},' >> $file
done

echo "\"controllers_Controller${number}.php\":{" >> $file
echo "\"url\":[\"~home${number}$~\"]," >> $file
echo "\"controller\":\"controllers\\/Controller${number}.php\"" >> $file
echo '}' >> $file
echo '}' >> $file


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