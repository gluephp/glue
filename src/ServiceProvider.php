<?php namespace Glue;

use Glue\App;
use Glue\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ServiceProvider implements Providers\ServiceProviderInterface
{
    public function register(App $app)
    {
        $app->singleton('Glue\App', function($app) {
            return $app;
        });

        // Routing
        $app->singleton('Glue\Router\Router');
        $app->alias('Glue\Router\Router', 'router');

        // HTTP
        $app->singleton('Glue\Http\Request', function() {
            return new Request(SymfonyRequest::createFromGlobals());
        });
        $app->alias('Glue\Http\Request', 'request');

        $app->singleton('Glue\Http\Response');
        $app->alias('Glue\Http\Response', 'response');

    }
}