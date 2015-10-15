<?php
/**
* Exception thrown by Netatmo SDK
*/
class NASDKException extends Exception
{
    public function __construct($code, $message)
    {
        parent::__construct($message, $code);
    }
}


class NASDKError
{
    const UNABLE_TO_CAST = 601;
    const NOT_FOUND = 602;
    const INVALID_FIELD = 603;
    const FORBIDDEN_OPERATION = 604;
}
?>
