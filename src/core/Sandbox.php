<?php
namespace BifrostRouter;
loadConfig();

class Sandbox{
    public static function runController($controller, $request){
        return call_user_func($controller, $request);    
    }

    public static function runScript($scriptPath) {
            require SCRIPTS_DIR . $scriptPath . '.php';
    }

    public static function runControllerData($controllerData, $routeName) {
        $controllerData->setRouteName($routeName);
        unset($routeName);

        $file = SCRIPTS_DIR . $controllerData->getScriptName() . '.php';
        require $file;
    }
}
