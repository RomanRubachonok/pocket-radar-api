<?php

namespace App\Services\PocketRadar\Api;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\PocketRadarResponse;

class Login extends AbstractApi
{
    /**
     * Attempt to login using the provided email and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function session(string $email, string $password)
    {
        $params = compact('email', 'password');

        return $this->client->send('POST', 'session', $params);
    }
}