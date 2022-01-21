<?php
namespace BifrostRouter;
loadConfig();

class Sandbox{
    public static function runController($controller, $request){
        require $controller;
        unset($controller);
        return \Controller::run($request);
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
