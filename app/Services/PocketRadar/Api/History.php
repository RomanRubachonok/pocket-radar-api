<?php

namespace App\Services\PocketRadar\Api;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\PocketRadarResponse;
use DateTimeInterface;

class History extends AbstractApi
{
    /**
     * Date format
     *
     * @const string
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * Get histories
     *
     * @param DateTimeInterface|string|null $dateStart
     * @param DateTimeInterface|string|null $dateEnd
     * @param DateTimeInterface|string|null $updatedAt
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function getHistories($dateStart = null, $dateEnd = null, $updatedAt = null)
    {
        $params = [];

        foreach (compact('dateStart', 'dateEnd', 'updatedAt') as $key => $date) {
            if ($date) {
                $params[$key] = $this->prepareDate($date, self::DATE_FORMAT);
            }
        }

        return $this->client->send('GET', 'histories', [], ['query' => $params]);
    }
}