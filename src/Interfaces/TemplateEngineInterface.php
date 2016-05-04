<?php namespace Glue\Interfaces;

interface TemplateEngineInterface
{

    /**
     * Add a template folder
     * 
     * @param string $path       Absolute path to folder
     * @param string $namespace  Prefixed name
     */
    public function addTemplateFolder($path, $namespace = null);
    

    /**
     * Render a template
     * 
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function render($template, array $params = []);

    
    /**
     * Add global variables for all templates
     * 
     * @param array $data
     */
    public function sharedData(array $data = []);

}