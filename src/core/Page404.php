<?php
namespace BifrostRouter;
class Page404 extends BaseController {
    public static function run($request) {
        echo '404: NOT FOUND';
    }
}
    
