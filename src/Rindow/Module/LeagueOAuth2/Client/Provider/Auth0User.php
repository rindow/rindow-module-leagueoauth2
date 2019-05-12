<?php
namespace Rindow\Module\LeagueOAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class Auth0User implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        if (array_key_exists('sub', $this->response)) {
            return $this->response['sub'];
        }
        return null;
    }

    /**
     * Get preferred display name.
     *
     * @return string
     */
    public function getName()
    {
        if (array_key_exists('name', $this->response)) {
            return $this->response['name'];
        }
        return null;
    }

    /**
     * Get preferred nickname.
     *
     * @return string
     */
    public function getNickname()
    {
        if (array_key_exists('nickname', $this->response)) {
            return $this->response['nickname'];
        }
        return null;
    }

    /**
     * Get email address.
     *
     * @return string|null
     */
    public function getEmail()
    {
        if (array_key_exists('email', $this->response)) {
            return $this->response['email'];
        }
        return null;
    }

    /**
     * Get avatar image URL.
     *
     * @return string|null
     */
    public function getAvatar()
    {
        if (array_key_exists('picture', $this->response)) {
            return $this->response['picture'];
        }
        return null;
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
