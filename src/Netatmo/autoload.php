<?php

    //PHP version check
    if (version_compare(PHP_VERSION, '5.4.0', '<'))
    {
        throw new Exception('Netatmo SDK requires PHP 5.3.0 or higher.');
    }

    //required libraries
    if (!function_exists('curl_version'))
    {
        throw new Exception('Netatmo SDK requires cURL extension.');
    }

    if(!function_exists('json_decode'))
    {
        throw new Exception('Netatmo SDK requires JSON extension.');
    }

    /**
    * Class autoloader for Netatmo SDK
    *
    * Based on PSR-4 autoloader example:
    * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
    * @param string $class The fully-qualified class name.
    * @return void
    */

    spl_autoload_register(function($class)
    {
        //project-specific namespace prefix
        $prefix = 'Netatmo\\';

        //base directory for the namespace prefix
        $baseDir = __DIR__.'/';

        //does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0)
        {
            //no move to the next registered autoloader
            return;
        }

        //get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDir . str_replace('\\', '/', $relative_class) . '.php';

        //if the file exists, require it
        if (file_exists($file))
        {
            require $file;
        }
    });
?>
