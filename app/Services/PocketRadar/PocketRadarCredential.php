<?php

namespace App\Services\PocketRadar;

class PocketRadarCredential implements PocketRadarCredentialInterface
{

    /**
     * The login
     *
     * @var string
     */
    protected $login;
    /**
     * The password
     *
     * @var string
     */
    protected $password;
    /**
     * The Access Token
     *
     * @var string
     */
    protected $accessToken;
    /**
     * Create credentials
     *
     * @param string $login     Application Client ID
     * @param string $password Application Client Secret
     * @param string $accessToken  Access Token
     */
    public function __construct($login, $password, $accessToken = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->accessToken = $accessToken;
    }

    /**
     * Get the App Client ID
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Get the App Client Secret
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the Access Token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}