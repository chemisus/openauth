<?php

namespace OpenAuth\Common;

use OpenAuth\Redirectable;

class Redirector implements Redirectable
{

    public function redirect($location)
    {
        header('Location: ' . $location);
    }
}