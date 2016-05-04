<?php namespace Glue\Interfaces;

use Glue\App;

interface ServiceProviderInterface
{
    /**
     * Register a service provider
     * @param  Glue\App    $app
     */
    public function register(App $app);
}