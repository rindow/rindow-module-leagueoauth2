<?php
namespace Rindow\Module\LeagueOAuth2\Handler;

use Rindow\Module\LeagueOAuth2\Exception;

class Oauth2Handler
{
    protected $config;
    protected $serviceLocator;
    protected $store;

    public function __construct($config=null,$serviceLocator=null,$store=null)
    {
        if($config)
            $this->setConfig($config);
        if($serviceLocator)
            $this->setServiceLocator($serviceLocator);
        if($store)
            $this->setStore($store);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function setStore($store)
    {
        $this->store = $store;
    }

    public function getProviderNames()
    {
        if(!isset($this->config['providers']))
            throw new Exception\RuntimeException('Oauth2 Providers are not specified.');
        return array_keys($this->config['providers']);
    }

    public function getProvider($providerName)
    {
        if(!isset($this->config['providers'][$providerName]))
            throw new Exception\RuntimeException('Oauth2 Provider "'.$providerName.'" is not found.');
        return $this->serviceLocator->get($this->config['providers'][$providerName]);
    }

    public function getAuthUri($providerName)
    {
        $provider = $this->getProvider($providerName);
        $authUrl = $provider->getAuthorizationUrl();
        $this->store->set('oauth2state',$provider->getState());
        return $authUrl;
    }

    public function confirm($providerName,$query)
    {
        if($query->get('error') != null) {
            $this->store->set('oauth2state',null);
            throw new Exception\RuntimeException('Error: '.htmlspecialchars($query->get('error'), ENT_QUOTES, 'UTF-8'));
        }
        if($query->get('state') == null ||
            $query->get('state')!=$this->store->get('oauth2state')) {
            $this->store->set('oauth2state',null);
            throw new Exception\RuntimeException('Invalid state for the oauth provider "'.$providerName.'".');
        }
        $provider = $this->getProvider($providerName);
        $token = $provider->getAccessToken('authorization_code', array(
            'code' => $query->get('code'),
        ));
        // Use this to interact with an API on the users behalf
        $token->getToken();
        // Use this to get a new access token if the old one expires
        $token->getRefreshToken();
        // Number of seconds until the access token will expire, and need refreshing
        $token->getExpires();
        // We got an access token, let's now get the owner details
        $ownerDetails = $provider->getResourceOwner($token);
        $this->store->set('token',$token);
        $this->store->set('resourceOwner',$ownerDetails);
        $this->store->set('firstname',$ownerDetails->getFirstName());
        $this->store->set('authname', $providerName.':'.$ownerDetails->getId());
    }

    public function getToken()
    {
        return $this->store->get('token');
    }

    public function getResourceOwner()
    {
        return $this->store->get('resourceOwner');
    }

    public function getAuthname()
    {
        return $this->store->get('authname');
    }

    public function getFirstname()
    {
        return $this->store->get('firstname');
    }
}