<?php

namespace OpenAuth\Google;

use OpenAuth\CodeProvider;
use OpenAuth\Exception\AuthorizeRedirectException;
use OpenAuth\Redirectable;
use OpenAuth\RequestGet;
use OpenAuth\RequestPost;
use OpenAuth\ResourceProvider;
use OpenAuth\Token;
use OpenAuth\TokenFactory;
use OpenAuth\TokenInitializer;
use OpenAuth\TokenRefresher;
use OpenAuth\UserAgent;

class GoogleOpenAuth implements ResourceProvider, UserAgent, TokenInitializer, TokenRefresher
{

    /**
     * @var CodeProvider
     */
    private $code_provider;

    /**
     * @var RequestGet
     */
    private $request_get;

    /**
     * @var RequestPost
     */
    private $request_post;

    /**
     * @var TokenFactory
     */
    private $token_factory;

    /**
     * @var Redirectable
     */
    private $redirect;

    private $token_uri = 'https://accounts.google.com/o/oauth2/token';

    private $auth_uri = 'https://accounts.google.com/o/oauth2/auth';

    private $redirect_uri;

    private $client_id;

    private $client_secret;

    private $scope;

    public function __construct(
        RequestGet $request_get,
        RequestPost $request_post,
        Redirectable $redirect,
        TokenFactory $token_factory,
        CodeProvider $code_provider,
        $redirect_uri,
        $client_id,
        $client_secret,
        array $scope = []
    ) {
        $this->code_provider = $code_provider;
        $this->request_get   = $request_get;
        $this->token_factory = $token_factory;
        $this->request_post  = $request_post;
        $this->redirect      = $redirect;
        $this->redirect_uri  = $redirect_uri;
        $this->client_id     = $client_id;
        $this->scope         = implode(' ', $scope);
        $this->client_secret = $client_secret;
    }

    public function resource(Token $token, $uri)
    {
        $headers = [
            'Authorization: OAuth ' . $token->tokenValue(),
        ];

        $fields = [
            'client_id'       => $this->client_id,
            'redirect_uri'    => $this->redirect_uri,
            'scope'           => $this->scope,
            'response_type'   => 'code',
            'approval_prompt' => 'force',
            'access_type'     => 'offline',
        ];

        $querystring = http_build_query($fields);

        $location = $uri . '?' . $querystring;

        return json_decode($this->request_get->get($uri, $headers));
    }

    public function initializeToken()
    {
        $code = $this->code_provider->code();

        if ($code === null) {
            $this->authorize();

            throw new AuthorizeRedirectException();
        }

        $fields = [
            'client_id'     => $this->client_id,
            'redirect_uri'  => $this->redirect_uri,
            'client_secret' => $this->client_secret,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'scope'         => '',
        ];

        $token = $this->fetchToken($fields);

        return $this->token_factory->make($token);
    }

    public function authorize()
    {
        $uri = $this->auth_uri;

        $fields = [
            'client_id'       => $this->client_id,
            'redirect_uri'    => $this->redirect_uri,
            'scope'           => $this->scope,
            'response_type'   => 'code',
            'approval_prompt' => 'force',
            'access_type'     => 'offline',
        ];

        $querystring = http_build_query($fields);

        $location = $uri . '?' . $querystring;

        $this->redirect->redirect($location);
    }

    public function fetchToken(array $fields = [])
    {
        $uri = $this->token_uri;

        return $this->request_post->post($uri, $fields);
    }

    public function refreshToken(Token $token)
    {
        $fields = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $token->refreshCode(),
            'grant_type'    => 'refresh_token',
        ];

        $object = $this->fetchToken($fields);

        return $this->token_factory->make($object, $token->refreshCode());
    }
}