<?php

namespace OpenAuth;

interface TokenFactory
{

    /**
     * @param string $value
     * @param string $refresh_code
     *
     * @return Token
     */
    public function make($value, $refresh_code = null);
}