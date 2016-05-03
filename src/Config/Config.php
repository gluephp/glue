<?php namespace Glue\Config;

use Maer\Config\Config as MaerConfig;

class Config extends MaerConfig
{
    /**
     * {@inheritdoc}
     */
    public function load($files, $forceReload = false)
    {
        if (!is_array($files)) {
            // Make it an array so we can use the same code
            $files = array($files);
        }

        foreach($files as $file) {

            if ((array_key_exists($file, $this->files) && !$forceReload) 
                || !is_file($file) || !is_readable($file)) {
                // It's already loaded, or doesn't exist, so let's skip it
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            $conf = $ext == "json"
                ? json_decode(file_get_contents($file), true, 512)
                : include $file;

            if (is_array($conf)) {
                // We're only interested if it is an array
                $this->conf         = array_replace_recursive($this->conf, $conf);
                $this->files[$file] = true;
            }

            unset($conf);
        }
    }

}