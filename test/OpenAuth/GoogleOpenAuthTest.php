<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Terrence.Howard
 * Date: 5/28/13
 * Time: 4:29 PM
 * To change this template use File | Settings | File Templates.
 */

namespace OpenAuth;

use Mockery;
use OpenAuth\Google\GoogleOpenAuth;
use PHPUnit_Framework_TestCase;

class GoogleOpenAuthTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var OpenAuth\CodeProvider
     */
    private $code;

    /**
     * @var OpenAuth\RequestGet
     */
    private $get;

    /**
     * @var OpenAuth\RequestPost
     */
    private $post;

    /**
     * @var OpenAuth\GoogleOpenAuth
     */
    private $google;

    /**
     * @var OpenAuth\TokenFactory
     */
    private $token_factory;

    /**
     * @var OpenAuth\Token
     */
    private $token;

    /**
     * @var OpenAuth\Redirect
     */
    private $redirect;

    public function setUp()
    {
        $this->get           = Mockery::mock('OpenAuth\RequestGet');
        $this->post          = Mockery::mock('OpenAuth\RequestPost');
        $this->redirect      = Mockery::mock('OpenAuth\Redirectable');
        $this->code          = Mockery::mock('OpenAuth\CodeProvider');
        $this->token         = Mockery::mock('OpenAuth\Token');
        $this->token_factory = Mockery::mock('OpenAuth\TokenFactory');
        $this->google        = new GoogleOpenAuth($this->get,
                                                  $this->post,
                                                  $this->redirect,
                                                  $this->token_factory,
                                                  $this->code);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testResource()
    {
        $result = '{}';

        $uri = 'https://example.com';

        $expect = json_decode($result);

        $this->get->shouldReceive('get')->once()->andReturn($result);

        $actual = $this->google->resource($this->token, $uri);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @expectedException \OpenAuth\Exception\AuthorizeRedirectException
     */
    public function testInitializeWithoutCode()
    {
        $this->code->shouldReceive('code')->once();

        $this->redirect->shouldReceive('redirect')->once();

        $this->google->initializeToken();
    }

    public function testInitializeWithCode()
    {
        $result = '{}';
        $uri    = 'https://example.com';
        $code   = 'somecode';
        $expect = $this->token;

        $this->code->shouldReceive('code')->once()->andReturn($code);

        $this->post->shouldReceive('post')->once()->andReturn($result);

        $this->token_factory->shouldReceive('make')->once()->with($result)->andReturn($this->token);

        $actual = $this->google->initializeToken();

        $this->assertEquals($expect, $actual);
    }

    public function testRefresh()
    {
        $result       = '{}';
        $uri          = 'https://example.com';
        $code         = 'somecode';
        $refresh_code = 'some refresh code';
        $token        = Mockery::mock('OpenAuth\Token');
        $expect       = $token;

        $this->post->shouldReceive('post')->once()->andReturn($result);

        $this->token_factory->shouldReceive('make')->once()->with($result, $refresh_code)->andReturn($token);

        $this->token->shouldReceive('refreshCode')->once()->andReturn($refresh_code);

        $actual = $this->google->refreshToken($this->token);

        $this->assertEquals($expect, $actual);
    }
}
