<?php

/**
 * use library without composer
 */

$autoloader = function ($class_name) {
    if (strpos($class_name, 'Vendloop')===0) {
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $file .= str_replace([ 'Vendloop\\', '\\' ], ['', DIRECTORY_SEPARATOR ], $class_name) . '.php';
        include_once $file;
    }
};

spl_autoload_register($autoloader);

return $autoloader;
