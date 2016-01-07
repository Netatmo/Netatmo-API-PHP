<?php

namespace Netatmo\Exceptions;

class NACurlErrorType extends NAClientException
{
    function __construct($code, $message)
    {
        parent::__construct($code, $message, CURL_ERROR_TYPE);
    }
}

?>
