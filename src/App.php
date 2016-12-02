<?php namespace Glue;

use Closure;
use Exception;
use Glue\Interfaces\ServiceProviderInterface;
use Illuminate\Container\Container;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\HandlerResolverInterface;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Symfony\Component\HttpFoundation\Response;

class App extends Container
{
    /**
     * @var array
     */
    protected $providers  = [];

    /**
     * Added methods
     *
     * @var array
     */
    protected $methods = [];


    public function __construct()
    {
        $this->register(
            $this->make('Glue\ServiceProvider')
        );
    }


    /**
     * Add a method to the container
     *
     * @param  string  $name
     * @param  Closure $closure
     *
     * @throws Exception Overriding a core method
     */
    public function addMethod($name, Closure $closure)
    {
        if (method_exists($this, $name)) {
            throw new Exception('Overriding core methods not allowed.');
        }

        $this->methods[$name] = $closure;
    }


    /**
     * Run an added container method
     *
     * @throws Exception Undefined method
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->methods)) {
            return call_user_func_array($this->methods[$method], $args);
        }

        throw new Exception("Undefined method $method in " . __CLASS__);
    }


    /**
     * Register a Service Provider
     *
     * @param  ServiceProviderInterface $provider
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        $this->providers[get_class($provider)] = $provider;
    }


    /**
     * Run the application and dispatch the router
     *
     * @param  string   $method
     * @param  string   $path
     */
    public function run($method = null, $path = null)
    {
        $method = $method ?: $this->request->getMethod();
        $path   = $path ?: $this->request->currentPath();

        $this->dispatchRouter($method, $path)->send();
    }


    /**
     * Dispatch the router
     *
     * @param  string $method
     * @param  string $path
     * @return mixed
     */
    protected function dispatchRouter($method, $path)
    {
        $resolver   = $this->make('Glue\Router\RouterResolver');
        $dispatcher = new Dispatcher($this->router->getData(), $resolver);

        $httpCode = Response::HTTP_OK;
        $allow    = null;

        try {

            $response = $dispatcher->dispatch($method, $path);
            if ($response instanceof Response) {
                $httpCode = $response->getStatusCode();
            }

        } catch(HttpRouteNotFoundException $e) {

            $response = $this->router->resolveErrorHandler(Response::HTTP_NOT_FOUND);
            $httpCode = Response::HTTP_NOT_FOUND;

        } catch(HttpMethodNotAllowedException $e) {

            $response = $this->router->resolveErrorHandler(Response::HTTP_METHOD_NOT_ALLOWED);
            $httpCode = Response::HTTP_METHOD_NOT_ALLOWED;
            if (strpos($e->getMessage(), 'Allow:') === 0) {
                // We got an Allow-message as exception message.
                // Save it so we can add it to the response header
                $allow = trim(explode(':', $e->getMessage())[1]);
            }

        } catch(\Exception $e) {

            if ($this->bound('Psr\Log\LoggerInterface')) {
                $this->make('Psr\Log\LoggerInterface')
                    ->critical($e->getMessage(), [__METHOD__]);
            }

            throw $e;

        }

        if (!$response instanceof Response) {
            $response = new Response((string) $response);
            $response->setStatusCode($httpCode);
        }

        if ($allow) {
            $response->headers->set('Allow', $allow);
        }

        return $response;
    }

}
