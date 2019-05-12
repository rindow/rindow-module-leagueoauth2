<?php
namespace Rindow\Module\LeagueOAuth2\Client\Provider;

use League\OAuth2\Client\Exception\HostedDomainException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Auth0 extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $domain;
    protected $audience;
    protected $scopes = [];
    protected $accessType;

    protected function getAuth0AccountDomain()
    {
        if(!$this->domain)
            throw new HostedDomainException('auth0 account domain is not specified.');
        return $this->domain;
    }

    public function getBaseAuthorizationUrl()
    {
        $domain = $this->getAuth0AccountDomain();
        return "https://${domain}/authorize";
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        $domain = $this->getAuth0AccountDomain();
        return "https://${domain}/oauth/token";
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $domain = $this->getAuth0AccountDomain();
        return "https://${domain}/userinfo";
    }

    protected function getAuthorizationParameters(array $options)
    {
        if (empty($options['audience']) && $this->audience) {
            $options['audience'] = $this->audience;
        }

        if (empty($options['access_type']) && $this->accessType) {
            $options['access_type'] = $this->accessType;
        }

        $scopes = array_merge($this->getDefaultScopes(), $this->scopes);

        if (!empty($options['scope'])) {
            $scopes = array_merge($scopes, $options['scope']);
        }

        $options['scope'] = array_unique($scopes);

        return parent::getAuthorizationParameters($options);
    }

    protected function getDefaultScopes()
    {
        return [
            'openid'
        ];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        // @codeCoverageIgnoreStart
        if (empty($data['error'])) {
            if($response->getStatusCode() < 400)
                return;
            $data['error'] = $response->getReasonPhrase();
        }
        // @codeCoverageIgnoreEnd

        $code = 0;
        $error = $data['error'];

        if (is_array($error)) {
            $code = $error['code'];
            $error = $error['message'];
        }

        throw new IdentityProviderException($error, $code, $data);
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new Auth0User($response);
    }
}
