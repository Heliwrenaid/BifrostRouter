# BifrostRouter

BifrostRouter is easy to learn microframework for creating web applications in PHP. It offers advanced routing system, integration with [Twig](https://twig.symfony.com/) template engine and follows MVC architecture.

## Requirements
```

PHP => 7.4

```

## Create new project
```

composer create-project heliwrenaid/bifrost-router-project:dev-main [PATH TO APP DIRECTORY]
cd /path/to/app/directory
composer bifrost-router install

```

## Setup existing project

Go to project root directory and run:

```

cd /path/to/app/directory
composer install
composer bifrost-router install

```

## Documentation

### Introduction

Every HTTP(S) request is redericted to index.php file, where BifrostRouter object is used.

```php

<?php
//session_start();
require 'vendor/autoload.php';

$router = new BifrostRouter\BifrostRouter(DEVELOPMENT_MODE);
$router->start();


```

When BifrostRouter instance is created all defined routes are loaded. Next, basing on HTTP(S) request a start() method will call a Controller class associated with proper Route.

### Config file

Inside project root directory there is a file named config.php. It contains defined constants, which describe directories structure of the project.
The most important constants are: CONTROLLERS_DIR, ROUTES_DIR, VIEWS_DIR.

### Routes

Route is an object, which contains:
- name: just a name of the route
- path: URL path, defined as regular expression
- controller: name of Controller class

Routes can be defined in three different ways using php, yaml and JSON files.


#### Simple example in Yaml

```yaml
routes:

  home:
    path: /home
    controller: HomeController
    
  articles:
    path: /arcticle/[0-9]+
    controller: ArticlesController
    
  users:
    path: /user/([0-9]+)
    controller: UsersController

groups:

  Post:
    prefix: /post/([0-9]+)
    routes:
      display:
        path: ''
        controller: PostController

      edit:
        path: /edit
        controller: PostController::edit

      delete:
        path: /delete
        controller: PostController::delete
```

Route files must be placed in ROUTE_DIR directory. Inside this directory there are 3 subdirectories:
- json
- php
- yaml

Routes defined in json file should be placed in json subdirectory ... etc

...


### Controllers

Each Controller is a static class which can handle multiple routes (using different static methods). Moreover, it must have proper namespace and extends \BifrostRouter\BaseController. When controller is called, then by default run() method is executed (if route doesn't indicate another method).

```php

<?php
namespace App\Controller;
class HomeController extends \BifrostRouter\BaseController {
    public static function run($request) {
        echo 'Hello World!';
    }
}

```
