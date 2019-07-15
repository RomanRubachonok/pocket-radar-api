<?php

namespace App\Services\PocketRadar\Api;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\PocketRadarResponse;

class Tags extends AbstractApi
{
    /**
     * Get sport tags
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function getSportsTags()
    {
        return $this->client->send('GET', 'users/settings/sports');
    }

    /**
     * Set sport tags
     *
     * @param array $customSports
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function setSportsTags(array $customSports = [])
    {
        return $this->client->send('PATCH', 'users/settings/sports', compact('customSports'));
    }

    /**
     * Get activities tags
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function getActivitiesTags()
    {
        return $this->client->send('GET', 'users/settings/activities');
    }

    /**
     * Set activities tags
     *
     * @param array $customActivities
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function setActivitiesTags(array $customActivities = [])
    {
        return $this->client->send('PATCH', 'users/settings/activities', compact('customActivities'));
    }
}