<?php
namespace Rindow\Module\LeagueOAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;

class GoogleAccessToken extends AccessToken
{
    protected $idToken;

    public function __construct(array $options = [])
    {
        if (!empty($options['id_token'])) {
            $this->idToken = $options['id_token'];
        }
        parent::__construct($options);
    }

    public function getIdToken()
    {
    	return $this->idToken;
    }
}
