# Glue - Almost a micro framework

> This package is still in development. I would suggest to wait until there's at least one tagged release.

Glue is really just, as the name suggests, the glue between some awesome 3rd party packages.
It's built to be extendable and only comes with the most basic features out of the box, like:

1. IoC Container - [illuminate/container](https://github.com/illuminate/container)
2. Router - [mrjgreen/phroute](https://github.com/mrjgreen/phroute)
3. Request - [symfony/http-foundation](https://github.com/symfony/http-foundation)
4. Response - [symfony/http-foundation](https://github.com/symfony/http-foundation)
5. Sessions - [symfony/http-foundation](https://github.com/symfony/http-foundation)
6. Config - [maer/config](https://github.com/magnus-eriksson/config)

## Installation

Use [Composer](http://getcomposer.org):

```bash
$ composer require gluephp/glue dev-develop
```

## Simple example

It basically works like any other micro framework:

```php
<?php

// Include Composers autoloader
include 'path/to/vendor/autoload.php';

// Initialize the app
$app = new Glue\App;

// Create a route
$app->router->get('/', function() {
    return 'Hello World';
});

// Run the app
$app->run();
```

## More...
...info will come

