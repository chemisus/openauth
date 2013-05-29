<?php

namespace OpenAuth;

interface RequestPost
{

    public function post($uri, array $fields = array(), array $headers = array());
}
