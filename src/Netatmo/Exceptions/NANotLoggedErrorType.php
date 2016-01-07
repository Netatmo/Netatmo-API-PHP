<?php

namespace Netatmo\Exceptions;

class NANotLoggedErrorType extends NAClientException
{
    function __construct($code, $message)
    {
        parent::__construct($code, $message, NOT_LOGGED_ERROR_TYPE);
    }
}

?>
