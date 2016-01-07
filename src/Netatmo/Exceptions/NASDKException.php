<?php

namespace Netatmo\Exceptions;

/**
* Exception thrown by Netatmo SDK
*/
class NASDKException extends \Exception
{
    public function __construct($code, $message)
    {
        parent::__construct($message, $code);
    }
}

?>
