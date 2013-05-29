<?php

namespace OpenAuth;

class Redirector implements Redirectable
{

    public function redirect($location)
    {
        header('Location: ' . $location);
    }
}