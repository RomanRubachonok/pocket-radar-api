<?php

namespace App\Services\PocketRadar\Api;

use App\Services\PocketRadar\PocketRadarClient;
use Carbon\Carbon;
use DateTimeInterface;

/**
 * Abstract class for Api classes.
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * @var PocketRadarClient
     */
    protected $client;

    /**
     * Date format
     *
     * @const string
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param PocketRadarClient $client
     */
    public function __construct(PocketRadarClient $client)
    {
        $this->client = $client;
    }

    /**
     * Prepare date
     *
     * @param DateTimeInterface|string|null $date
     * @param string $format
     *
     * @return string
     */
    protected function prepareDate($date, string $format = self::DATE_FORMAT)
    {
        if (!($date instanceof DateTimeInterface)) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }
}
