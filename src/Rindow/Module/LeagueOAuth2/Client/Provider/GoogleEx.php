<?php
namespace Rindow\Module\LeagueOAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Grant\AbstractGrant;

class GoogleEx extends Google
{
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        return new GoogleAccessToken($response);
    }
}
