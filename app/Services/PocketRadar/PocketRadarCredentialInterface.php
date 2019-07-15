<?php

namespace App\Services\PocketRadar;

interface PocketRadarCredentialInterface
{
    /**
     * Get the App Client ID
     *
     * @return string
     */
    public function getLogin();

    /**
     * Get the App Client Secret
     *
     * @return string
     */
    public function getPassword();
    /**
     * Get the Access Token
     *
     * @return string
     */
    public function getAccessToken();
}