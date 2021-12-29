#!/bin/bash
if [[ $1 == '' ]]; then
    echo 'ERROR: enter number of routes'
    exit
fi

number=$1
offset=$1
start=1
controllersDir='controllers'

file='routes/yaml/routes.yaml'
echo '' > $file
for i in `seq $start $number`; do
echo "Controller${i}:
    url: ~home${i}\$~" >> $file

done

let "start=number+1";
let "number=number+offset";


file='routes/php/routes.php'
echo '<?php' > $file
echo 'return [' >> $file
for i in `seq $start $number`; do
    echo "'Controller${i}' => ['url' => '~home${i}$~']," >> $file
done
echo '];' >> $file

let "start=number+1";
let "number=number+offset";

file='routes/json/routes.json'
echo '{' > $file
let "num=number-1";
for i in `seq $start $num`; do
    echo "\"Controller${i}\":{" >> $file
    echo "\"url\": \"~home${i}$~\"" >> $file
    echo '},' >> $file
done

echo "\"Controller${number}\":{" >> $file
echo "\"url\": \"~home${number}$~\"" >> $file
echo '}' >> $file
echo '}' >> $file


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