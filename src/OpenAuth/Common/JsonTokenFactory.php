<?php

namespace OpenAuth\Common;

use Exception;
use OpenAuth\Exception\TokenErrorException;
use OpenAuth\Token;
use OpenAuth\TokenFactory;

class JsonTokenFactory implements TokenFactory
{

    /**
     * @param string $value
     * @param string $refresh_code
     *
     * @return Token
     */
    public function make($value, $refresh_code = null)
    {
        $object = json_decode($value);

        if (isset($object->error)) {
            throw new TokenErrorException();
        }

        if ($refresh_code !== null) {
            $object->refresh_code = $refresh_code;
        }

        return new CountdownToken($object->access_token,
                                  $object->token_type,
                                  $object->refresh_token,
                                  $object->expires_in);
    }
}