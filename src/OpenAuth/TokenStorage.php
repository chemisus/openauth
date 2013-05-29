<?php

namespace OpenAuth;

interface TokenStorage
{

    public function saveToken(Token $token);

    public function loadToken();
}
