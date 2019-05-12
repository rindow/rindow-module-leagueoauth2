<?php
namespace RindowTest\Module\LeagueOAuth2\Handler\Oauth2HandlerTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Dict;
use Rindow\Module\LeagueOAuth2\Handler\Oauth2Handler;

class TestToken
{
	public function getToken()
	{
		return 'fooToken';
	}

	public function getRefreshToken()
	{
		# code...
	}

	public function getExpires()
	{
		# code...
	}
}

class TestOwnerDetails
{
	public function getFirstName()
	{
		return 'foo';
	}

	public function getId()
	{
		return 'fooId';
	}
}

class TestProvider
{
	public function getAuthorizationUrl()
	{
		return '/confirm/fooProvider';
	}

	public function getAccessToken($code,array $properties)
	{
		return new TestToken();
	}

	public function getResourceOwner($token)
	{
		return new TestOwnerDetails();
	}

	public function getState()
	{
		return 'fooState';
	}
}

class Test extends TestCase
{
	public function getServiceLocator()
	{
		$container = new Dict();
		$container->set(__NAMESPACE__.'\TestProvider', new TestProvider());
		return $container;
	}

	public function testNormal()
	{
		$config = array(
			'providers' => array(
				'fooProvider' => 'RindowTest\Module\LeagueOAuth2\Handler\Oauth2HandlerTest\TestProvider',
			),
		);
		$handler = new Oauth2Handler($config,$this->getServiceLocator(),new Dict());

		$this->assertEquals('/confirm/fooProvider',$handler->getAuthUri($providerName='fooProvider'));
		$query = new Dict(array('state'=>'fooState','redirectUrl'=>'/test/test/test'));
		$handler->confirm($providerName='fooProvider',$query);
		$this->assertEquals('fooProvider:fooId',$handler->getAuthname());
		$this->assertEquals('foo',$handler->getFirstname());
	}

    /**
     * @expectedException        Rindow\Module\LeagueOAuth2\Exception\RuntimeException
     * @expectedExceptionMessage Invalid state for the oauth provider "fooProvider"
	 */
	public function testInvalidState()
	{
		$config = array(
			'providers' => array(
				'fooProvider' => 'RindowTest\Module\LeagueOAuth2\Handler\Oauth2HandlerTest\TestProvider',
			),
		);
		$handler = new Oauth2Handler($config,$this->getServiceLocator(),new Dict());

		$query = new Dict(array('state'=>'fooState','redirectUrl'=>'/test/test/test'));
		$handler->confirm($providerName='fooProvider',$query);
	}

    /**
     * @expectedException        Rindow\Module\LeagueOAuth2\Exception\RuntimeException
     * @expectedExceptionMessage Oauth2 Provider "barProvider" is not found.
	 */
	public function testProviderNotFound()
	{
		$config = array(
			'providers' => array(
				'fooProvider' => 'RindowTest\Module\LeagueOAuth2\Handler\Oauth2HandlerTest\TestProvider',
			),
		);
		$handler = new Oauth2Handler($config,$this->getServiceLocator(),new Dict());
		$handler->getAuthUri($providerName='barProvider');
	}

    /**
     * @expectedException        Rindow\Module\LeagueOAuth2\Exception\RuntimeException
     * @expectedExceptionMessage Error: error error.
	 */
	public function testError()
	{
		$config = array(
			'providers' => array(
				'fooProvider' => 'RindowTest\Module\LeagueOAuth2\Handler\Oauth2HandlerTest\TestProvider',
			),
		);
		$handler = new Oauth2Handler($config,$this->getServiceLocator(),new Dict());
		$this->assertEquals('/confirm/fooProvider',$handler->getAuthUri($providerName='fooProvider'));
		$query = new Dict(array('error'=>'error error.','state'=>'fooState','redirectUrl'=>'/test/test/test'));
		$handler->confirm($providerName='fooProvider',$query);
	}
}