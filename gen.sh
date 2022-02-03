#!/bin/bash

function generateController {
    start=$1
    stop=$2
    name=$3
    for i in `seq $start $stop`; do
    controller="${controllersDir}/${name}${i}.php"
echo "<?php
namespace App\Controller;
class ${name}${1} extends \BifrostRouter\BaseController {
    public static function run(\$request) {
        echo 'echo from controller defined in ${name}${i} route';
    }
}" > $controller
    done
}


if [[ $1 == '' ]]; then
    echo 'ERROR: enter number of routes'
    exit
fi

number=$1
offset=$1
start=1
controllersDir='controllers'

generateController $start $number 'Yaml'

startYaml=$start
file='routes/yaml/routes_test.yaml'
echo 'routes:' > $file
for i in `seq $start $number`; do
    echo "
    yaml$i:
        path: /yaml$i
        controller: Yaml$1
    " >> $file
done

let "start=number+1";
let "number=number+offset";

generateController $start $number 'Php'

file='routes/php/routes_test.php'
echo "
<?php
return [
    'routes' => [
" > $file
for i in `seq $start $number`; do
    echo "
    'route$i' => [
        'path' => '/php$i',
        'controller' => 'Php$i'
    ],
    " >> $file
done
echo "
    ]
];
" >> $file

let "start=number+1";
let "number=number+offset";

generateController $start $number 'Json'

file='routes/json/routes_test.json'
echo '
{
    "routes": {
' > $file

let "num=number-1";
for i in `seq $start $num`; do
    echo "
    \"json$i\": {
            \"path\": \"/json$i\",
            \"controller\": \"Json$i\"
        },
    " >> $file
done
 echo "
    \"json$number\": {
            \"path\": \"/json$number\",
            \"controller\": \"Json$number\"
        }
    " >> $file

echo "
    }
}
" >> $file


php build.php
