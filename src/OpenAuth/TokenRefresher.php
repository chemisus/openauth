<?php

namespace OpenAuth;

interface TokenRefresher
{

    public function refreshToken(Token $token);
}
