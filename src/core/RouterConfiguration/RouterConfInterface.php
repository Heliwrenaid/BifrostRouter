<?php

/* OPTIONS
['routeOnceTime' => true] (default: true) match only first route
['rederictFromTrailingSlash' => true] (default: false) rederict from urls with trailing slash
['filterPost'] (default: false) filter whole $_POST 
*/

interface RouterConfigurationInterface{
    public function getOption($name);
    public function getRoutes();

    public function getOptions();
    public function get404Page();
}