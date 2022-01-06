<?php
namespace BifrostRouter;
use Exception;
loadConfig();

class ScriptSandbox{
    public static function runScript($scriptPath) {
            require SCRIPTS_DIR . $scriptPath . '.php';
    }

    public static function runControllerData($controllerData, $routeName) {
        $controllerData->setRouteName($routeName);
        unset($routeName);

        $file = SCRIPTS_DIR . $controllerData->getScriptName() . '.php';
        require $file;
        // if (function_exists($scriptName)) {
        //     $controllerData->setRouteName($routeName);
        //     call_user_func($scriptName, $controllerData);
        // } else {
        //     throw new Exception('Function "' . $scriptName . '" not found in ' . $file);
        // }
    }
}
