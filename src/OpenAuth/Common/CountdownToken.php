<?php

namespace OpenAuth\Common;

use OpenAuth\Token;

class CountdownToken implements Token
{

    private $value;

    private $type;

    private $refresh_code;

    private $expires;

    public function __construct($value, $type, $refresh_code, $expires)
    {
        $this->value        = $value;
        $this->type         = $type;
        $this->refresh_code = $refresh_code;
        $this->expires      = $expires;
    }

    public function tokenValue()
    {
        return $this->value;
    }

    public function tokenType()
    {
        return $this->type;
    }

    public function refreshCode()
    {
        return $this->refresh_code;
    }

    public function tokenExpires()
    {
        return $this->expires;
    }

    public function isTokenExpired()
    {
    }
}