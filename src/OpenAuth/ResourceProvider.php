<?php

namespace OpenAuth;

interface ResourceProvider
{

    public function resource(Token $token, $uri);
}
