<?php
namespace BifrostRouter;

use Exception;

loadConfig();
class Response{
    private static $twigCacheFile = CACHE_DIR . 'twig.cache';
    private static $urlResolver = CONFIG_DIR . 'name2url';

    public static function loadTwig(){
        if (file_exists(self::$twigCacheFile)) {
            return unserialize(file_get_contents(self::$twigCacheFile));			
        } else {
            $twig = self::twigSetup();
            self::saveTwig($twig);
            return $twig;
        }
    }

    public static function saveTwig($obj) {
		file_put_contents(self::$twigCacheFile, serialize($obj));
	}

    private static function twigSetup(){
        $loader = new \Twig\Loader\FilesystemLoader(VIEWS_DIR);
        return new \Twig\Environment($loader,array());
    }

    public static function display($template, $data = null){
        $twig = self::loadTwig();
        if($data == null){
            $twig->display($template);
        } else {
            $twig->display($template, $data);
        }
    }

    public static function render($routeName, $data = null){
        $twig = self::loadTwig();
        if($data == null){
            $twig->display(self::routeNameToTemplate($routeName));
        } else {
            $twig->display(self::routeNameToTemplate($routeName), $data);
        }
    }

    private static function routeNameToTemplate($name) {
        return str_replace('-', '/', $name) . '.html.twig';
    }

    public static function json($data = [], $status = 200){
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function rederict() {
        $routeName = func_get_arg(0);
        $data = func_get_args();
        unset($data[0]);

        if (file_exists(self::$urlResolver)) {
            $handle = fopen(self::$urlResolver, 'r');
            if ($handle) {

                $regex = null;

                while (($line = fgets($handle)) !== false) {
                    if(!empty($line)) {
                        $line = json_decode($line, true);
                        if ($line['name'] === $routeName) {
                            $regex = $line['path'];
                            break;
                        }
                    }
                }
                fclose($handle);

                if ($regex === null) {
                    throw new Exception('Route name was not found: consider running `php build.php`');
                    return false;
                }

                $url = substr($regex, 2, -2);
                foreach ($data as $value) {
                    preg_match('~(\(.*\))~U', $url, $matches);
                    $matches = array_unique($matches);
                    if (isset($matches[0])){
                        $url = str_replace($matches[0], $value, $url);
                    } else {
                        throw new Exception('Passed too much values for path regex: ' . $regex);
                    }
                }

                if (preg_match('~(\(.*\))~', $url)) {
                    throw new Exception('Passed not enough values for path regex: ' . $regex);
                }
                
                if (!preg_match($regex, $url)) {
                    throw new Exception('Passed unproper values for path regex: ' . $regex);
                }
                
            } else {
                throw new Exception('In Response::rederict can\'t open config file');
            } 
        } else {
            throw new Exception('Response config file is not found: run `php build.php`');
        }
        header('Location: ' . $url);
        exit();
    }

    public static function getUrlResolverPath() {
        return self::$urlResolver;
    }
}