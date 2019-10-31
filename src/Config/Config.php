<?php


namespace Two\Config;


use Two\Util\Arrays;

class Config
{
    private $config = [];

    public function __construct(array $configFolders, array $env)
    {
        foreach ( $configFolders as $configFolder ) {
            foreach ( glob($configFolder . '/*.php') as $file ) {
                $group = str_replace('.php', '', basename($file));
                $properties = require $file;

                if ( isset($this->config[$group]) ) {
                    $this->config[$group] = Arrays::mergeRecursive($this->config[$group], $properties, true);
                } else {
                    $this->config[$group] = $properties;
                }
            }
        }
    }

    public function get($key, $default = null)
    {
        if (empty($key)) {
            return $this->config;
        }

        return Arrays::deepGet($this->config, $key, $default);
    }
}