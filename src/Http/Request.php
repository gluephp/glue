<?php namespace Glue\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;


    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(SymfonyRequest $request = null)
    {
        $this->request = $request ?: SymfonyRequest::createFromGlobals();
    }


    /**
     * Get a query value from the $_GET super global
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function get($key = null, $fallback = null)
    {
        return $key
            ? $this->request->query->get($key, $fallback)
            : $this->request->query;
    }


    /**
     * Get a parameter value from the $_POST super global
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function post($key = null, $fallback = null)
    {
        return $key
            ? $this->request->request->get($key, $fallback)
            : $this->request->request;
    }


    /**
     * Get a cookie value from the $_COOKIE super global
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function cookies($key = null, $fallback = null)
    {
        return $key
            ? $this->request->cookies->get($key, $fallback)
            : $this->request->cookies;
    }


    /**
     * Get a file value from the $_FILES super global
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function files($key = null, $fallback = null)
    {
        return $key
            ? $this->request->files->get($key, $fallback)
            : $this->request->files;
    }


    /**
     * Get a server value from the $_SERVER super global
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function server($key = null, $fallback = null)
    {
        return $key
            ? $this->request->server->get($key, $fallback)
            : $this->request->server;
    }


    /**
     * Get a request header value
     *
     * @param  string   $key
     * @param  mixed    $fallback
     * @return mixed
     */
    public function headers($key = null, $fallback = null)
    {
        return $key
            ? $this->request->headers->get($key, $fallback)
            : $this->request->headers;
    }


    /**
     * If the method doesn't exist here, let's forward the call
     * to the main request instance
     */
    public function __call($method, array $args)
    {
        if (!method_exists($this->request, $method)) {
            throw new \Exception("Call to undefined method " . $method);
        }

        return call_user_func_array([$this->request, $method], $args);
    }


    /**
     * Get the current path
     *
     * @return string
     */
    public function currentPath()
    {
        return trim(
            strtok($this->request->getRequestUri(), '?'),
            '/'
        );
    }
}
