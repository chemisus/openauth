<?php

namespace OpenAuth;

class OpenAuthTemplate implements TokenProvider
{

    /**
     * @var Token
     */
    private $token;

    /**
     * @var TokenInitializer
     */
    private $token_initializer;

    /**
     * @var TokenRefresher
     */
    private $token_refresher;

    /**
     * @var TokenStorage
     */
    private $token_storage;

    /**
     * @var ResourceProvider
     */
    private $resource_provider;

    public function __construct(TokenStorage $token_storage,
                                TokenInitializer $token_initializer,
                                TokenRefresher $token_refresher,
                                ResourceProvider $resource_provider)
    {
        $this->token_storage     = $token_storage;
        $this->token_initializer = $token_initializer;
        $this->token_refresher   = $token_refresher;
        $this->resource_provider = $resource_provider;
    }

    public function resource($uri)
    {
        $token = $this->token();

        return $this->resource_provider->resource($token, $uri);
    }

    public function token()
    {
        if ($this->token === null) {
            $this->reloadToken();
        }

        if ($this->token === null) {
            $this->initializeToken();
        }

        if ($this->token->isTokenExpired()) {
            $this->refreshToken();
        }

        return $this->token;
    }

    public function reloadToken()
    {
        $this->token = $this->token_storage->loadToken();
    }

    public function initializeToken()
    {
        $this->token = $this->token_initializer->initializeToken();

        $this->token_storage->saveToken($this->token);
    }

    public function refreshToken()
    {
        $this->token = $this->token_refresher->refreshToken($this->token);

        $this->token_storage->saveToken($this->token);
    }
}
