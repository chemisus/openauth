<?php

namespace OpenAuth;

use Mockery;
use OpenAuth\OpenAuthTemplate;
use OpenAuth\GoogleTokenInitializer;
use PHPUnit_Framework_TestCase;

class OpenAuthTemplateTest extends PHPUnit_Framework_TestCase
{

    private $oauth;

    private $storage;

    private $initializer;

    private $refresher;

    private $token;

    private $resource;

    public function setUp()
    {
        $this->storage     = Mockery::mock('OpenAuth\TokenStorage');
        $this->initializer = Mockery::mock('OpenAuth\TokenInitializer');
        $this->refresher   = Mockery::mock('OpenAuth\TokenRefresher');
        $this->token       = Mockery::mock('OpenAuth\Token');
        $this->resource    = Mockery::mock('OpenAuth\ResourceProvider');

        $this->oauth = new OpenAuthTemplate($this->storage,
                                              $this->initializer,
                                              $this->refresher,
                                              $this->resource);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testTokenWhenStored()
    {
        $this->storage->shouldReceive('loadToken')->once()->andReturn($this->token);

        $this->token->shouldReceive('isTokenExpired')->once()->andReturn(false);

        $expect = $this->token;

        $actual = $this->oauth->token();

        $this->assertEquals($expect, $actual);
    }

    public function testTokenWhenInitialized()
    {
        $this->storage->shouldReceive('loadToken')->once();

        $this->storage->shouldReceive('saveToken')->once()->with($this->token);

        $this->initializer->shouldReceive('initializeToken')->once()->andReturn($this->token);

        $this->token->shouldReceive('isTokenExpired')->once()->andReturn(false);

        $expect = $this->token;

        $actual = $this->oauth->token();

        $this->assertEquals($expect, $actual);
    }

    public function testTokenWhenRefreshed()
    {
        $token = Mockery::mock('OpenAuth\Token');

        $this->storage->shouldReceive('loadToken')->once()->andReturn($this->token);

        $this->storage->shouldReceive('saveToken')->once()->with($token);

        $this->refresher->shouldReceive('refreshToken')->once()->andReturn($token);

        $this->token->shouldReceive('isTokenExpired')->once()->andReturn(true);

        $expect = $token;

        $actual = $this->oauth->token();

        $this->assertEquals($expect, $actual);
    }
}
