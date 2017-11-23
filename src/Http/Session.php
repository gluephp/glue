<?php namespace Glue\Http;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class Session
{
    /**
     * @var Symfony\Component\HttpFoundation\Session
     */
    protected $session;


    /**
     * @param Symfony\Component\HttpFoundation\Session $session
     */
    public function __construct(SymfonySession $session = null)
    {
        $this->session = $session ?: new SymfonySession;
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            $this->session->start();
        }
    }


    /**
     * Set a flash message
     *
     * @param string    $key
     * @param mixed     $value
     */
    public function setFlash($key, $value)
    {
        $this->session->getFlashBag()->set($key, $value);
        return $this;
    }


    /**
     * Add a flash message
     *
     * @param string    $key
     * @param mixed     $value
     */
    public function addFlash($key, $value)
    {
        $this->session->getFlashBag()->add($key, $value);
        return $this;
    }


    /**
     * Get a flash message
     *
     * @param  string   $key
     * @param  array    $fallback
     * @return mixed
     */
    public function getFlash($key, $fallback = [])
    {
        return $this->session->getFlashBag()->get($key, $fallback);
    }


    /**
     * Clear flash messages
     *
     * @param mixed     $value
     */
    public function clearFlash($key, $value)
    {
        $this->session->getFlashBag()->clear();
        return $this;
    }


    /**
     * If the method doesn't exist here, let's forward the call
     * to the main session instance
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->session, $method)) {
            throw new \Exception("Call to undefined method " . $method);
        }

        return call_user_func_array([$this->session, $method], $args);
    }
}
