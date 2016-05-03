<?php namespace Glue;

use Glue\Providers\ServiceProviderInterface;
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
    protected $providers = [];


    public function __construct()
    {
        $this->register(
            $this->make('Glue\ServiceProvider')
        );
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
        $path   = $path ?: $this->request->getPathInfo();

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

            throw new \Exception("Unhandled route exception");

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