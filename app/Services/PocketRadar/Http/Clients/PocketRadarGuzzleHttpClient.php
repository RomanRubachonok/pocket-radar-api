<?php

namespace App\Services\PocketRadar\Http\Clients;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\Http\PocketRadarRawResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PocketRadarGuzzleHttpClient.
 */
class PocketRadarGuzzleHttpClient implements PocketRadarHttpClientInterface
{
    /**
     * GuzzleHttp client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new PocketRadarGuzzleHttpClient instance.
     *
     * @param Client $client GuzzleHttp Client
     */
    public function __construct(Client $client = null)
    {
        //Set the client
        $this->client = $client ?: new Client();
    }

    /**
     * Send request to the server and fetch the raw response.
     *
     * @param string $url URL/Endpoint to send the request to
     * @param string $method Request Method
     * @param string|resource|StreamInterface $body Request Body
     * @param array $headers Request Headers
     * @param array $options Additional Options
     *
     * @return PocketRadarRawResponse Raw response from the server
     *
     * @throws PocketRadarClientException
     * @throws GuzzleException
     */
    public function send($url, $method, $body, $headers = [], $options = [])
    {
        //Create a new Request Object
        $request = new Request($method, $url, $headers, $body);

        try {
            //Send the Request
            $rawResponse = $this->client->send($request, $options);
        } catch (BadResponseException $e) {
            throw new PocketRadarClientException($e->getResponse()->getBody(), $e->getCode(), $e);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();

            if (!$rawResponse instanceof ResponseInterface) {
                throw new PocketRadarClientException($e->getMessage(), $e->getCode());
            }
        }

        //Something went wrong
        if ($rawResponse->getStatusCode() >= 400) {
            throw new PocketRadarClientException($rawResponse->getBody());
        }

        $body = $this->getResponseBody($rawResponse);
        $rawHeaders = $rawResponse->getHeaders();
        $httpStatusCode = $rawResponse->getStatusCode();

        //Create and return a PocketRadarRawResponse object
        return new PocketRadarRawResponse($rawHeaders, $body, $httpStatusCode);
    }

    /**
     * Get the Response Body.
     *
     * @param string|ResponseInterface $response Response object
     *
     * @return string
     */
    protected function getResponseBody($response)
    {
        //Response must be string
        $body = $response;

        if ($response instanceof ResponseInterface) {
            //Fetch the body
            $body = $response->getBody();
        }

        if ($body instanceof StreamInterface) {
            $body = $body->getContents();
        }

        return (string) $body;
    }
}
