<?php
namespace App\Services\PocketRadar\Http\Clients;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\Http\PocketRadarRawResponse;

/**
 * PocketRadarHttpClientInterface
 */
interface PocketRadarHttpClientInterface
{
    /**
     * Send request to the server and fetch the raw response
     *
     * @param  string $url     URL to send the request to
     * @param  string $method  Request Method
     * @param  string|resource|null $body Request Body
     * @param  array  $headers Request Headers
     * @param  array  $options Additional Options
     *
     * @return PocketRadarRawResponse Raw response from the server
     *
     * @throws PocketRadarClientException
     */
    public function send($url, $method, $body, $headers = [], $options = []);
}
