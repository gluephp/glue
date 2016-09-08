<?php namespace Glue\Router;

use Glue\App;
use Phroute\Phroute\HandlerResolverInterface;

class RouterResolver implements HandlerResolverInterface
{
    protected $container;

    public function __construct(App $container)
    {
        $this->container = $container;
    }

    public function resolve($handler)
    {
        // Just to be cool, let's allow controllers in the format:
        // ControllerName@methodName
        if (is_array($handler) && strpos($handler, '@') !== false) {
            $handler = explode('@', $handler);
        }

        if (is_array($handler) and is_string($handler[0])) {
            $handler[0] = $this->container->make($handler[0]);
        }

        return $handler;
    }
}