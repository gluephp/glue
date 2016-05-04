<?php namespace Glue\Interfaces;

interface TemplateEngineInterface
{
    /**
     * Add a template folder
     * 
     * @param string $path  Absolute path to folder
     * @param string $name  Prefixed name
     */
    public function setTemplatePath($path, $name = null);
    
    /**
     * Render a template
     * 
     * @param  string $template
     * @param  array  $data
     * @return string
     */
    public function render($template, array $data = []);
    
    /**
     * Add global variables for all templates
     * 
     * @param array $data
     */
    public function addGlobal(array $data = []);
    
}