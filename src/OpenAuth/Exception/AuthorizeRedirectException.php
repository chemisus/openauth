<?php

namespace OpenAuth\Exception;

use Exception;

class AuthorizeRedirectException extends Exception
{

    private $location;

    public function __construct($location = "", $message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->location = $location;
    }

    public function location()
    {
        return $this->location;
    }
}