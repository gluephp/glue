<?php namespace Glue\Router;

use Closure;
use Phroute\Phroute\RouteCollector;

class Router extends RouteCollector
{

    /**
     * @var array
     */
    protected $errorHandlers = [];


    /**
     * Set callback for route not found
     *
     * @param  Closure $callback
     */
    public function notFound(Closure $callback)
    {
        $this->errorHandlers[404] = $callback;
    }


    /**
     * Set callback for method not allowed
     *
     * @param  Closure $callback
     */
    public function methodNotAllowed(Closure $callback)
    {
        $this->errorHandlers[405] = $callback;
    }


    /**
     * Resolve an error handler
     *
     * @return [type] [description]
     */
    public function resolveErrorHandler($errorCode)
    {
        return array_key_exists($errorCode, $this->errorHandlers)
            ? call_user_func_array($this->errorHandlers[$errorCode], [])
            : '';
    }


    /**
     * Resolve a named route
     *
     * @param   $name
     * @param   array   $args
     * @param   boolean $noInitialSlash
     * @return  string
     */
    public function route($name, array $args = null, $noInitialSlash = false)
    {
        $route = ltrim(parent::route($name, $args), '/');

        return $noInitialSlash
            ? $route
            : '/' . $route;
    }

}