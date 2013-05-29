<?php

namespace OpenAuth;

interface RequestGet
{

    public function get($uri, array $headers = array());
}
