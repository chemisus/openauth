<?php

namespace OpenAuth;

interface Token
{

    public function tokenValue();

    public function tokenType();

    public function refreshCode();

    public function tokenExpires();

    public function isTokenExpired();
}
