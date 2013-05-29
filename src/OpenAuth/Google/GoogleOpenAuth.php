<?php

namespace OpenAuth\Google;

use OpenAuth\Exception\AuthorizeRedirectException;
use OpenAuth\ResourceProvider;
use OpenAuth\UserAgent;
use OpenAuth\TokenInitializer;
use OpenAuth\TokenRefresher;
use OpenAuth\Token;
use OpenAuth\RequestPost;
use OpenAuth\RequestGet;
use OpenAuth\TokenFactory;
use OpenAuth\CodeProvider;
use OpenAuth\Redirectable;

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

    public function __construct(RequestGet $request_get,
                                RequestPost $request_post,
                                Redirectable $redirect,
                                TokenFactory $token_factory,
                                CodeProvider $code_provider)
    {
        $this->code_provider = $code_provider;
        $this->request_get   = $request_get;
        $this->token_factory = $token_factory;
        $this->request_post  = $request_post;
        $this->redirect      = $redirect;
    }

    public function resource(Token $token, $uri)
    {
        $uri = 'https://accounts.google.com/o/oauth2/auth
        ?redirect_uri=https://developers.google.com/oauthplayground
        &response_type=code
        &client_id=407408718192.apps.googleusercontent.com
        &approval_prompt=force
        &scope=https://www.googleapis.com/auth/userinfo.profile
        &access_type=offline';

        $headers = [];

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
            'client_id'     => '407408718192.apps.googleusercontent.com',
            'client_secret' => '************',
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => 'https://developers.google.com/oauthplayground',
            'scope'         => '',
        ];

        $token = $this->fetchToken($fields);

        return $this->token_factory->make($token);
    }

    public function authorize()
    {
        $uri = '';

        $this->redirect->redirect($uri);
    }

    public function fetchToken(array $fields = [])
    {
        $uri = 'https://accounts.google.com/o/oauth2/token';

        $headers = [];

        return $this->request_post->post($uri, $fields, $headers);
    }

    public function refreshToken(Token $token)
    {
        $fields = [
            'client_id'     => '407408718192.apps.googleusercontent.com',
            'client_secret' => '',
            'grant_type'    => 'refresh_token',
            'refresh_token' => '1/SAJMGoDQSQ62XdJ-1QktM2KkjrYbFXKsjoT5fcZI7vM',
        ];

        $object = $this->fetchToken($fields);

        return $this->token_factory->make($object, $token->refreshCode());
    }
}