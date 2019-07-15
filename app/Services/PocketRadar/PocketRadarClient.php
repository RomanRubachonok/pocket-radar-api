<?php

namespace App\Services\PocketRadar;

use App\Services\PocketRadar\Api\ApiInterface;
use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\Http\Clients\PocketRadarHttpClientInterface;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * Class PocketRadarClient
 *
 * @method Api\Login login()
 * @method Api\History history()
 * @method Api\Tags tags()
 */
class PocketRadarClient
{
    /**
     * PocketRadar API Root URL.
     *
     * @const string
     */
    const BASE_PATH = 'https://app.pocketradar.com/api';

    /**
     * Api version
     *
     * @const string
     */
    const V1 = 'v1';

    /**
     * Api version
     *
     * @const string[]
     */
    const API_VERSIONS = [
        self::V1,
    ];

    /**
     * PocketRadarHttpClientInterface Implementation
     *
     * @var PocketRadarHttpClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * Access Token
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Pocket Radar credential
     *
     * @var PocketRadarCredential
     */
    private $credential;


    /**
     * PocketRadarClient constructor.
     *
     * @param PocketRadarHttpClientInterface $httpClient
     * @param string $apiVersion
     */
    public function __construct(PocketRadarHttpClientInterface $httpClient, string $apiVersion = null) {
        $this->httpClient = $httpClient;
        $this->apiVersion = $apiVersion ?? self::V1;
    }

    /**
     * Get api
     *
     * @param string $name
     *
     * @return ApiInterface
     * @throws InvalidArgumentException
     *
     */
    public function api($name)
    {
        switch ($name) {
            case 'login':
                $api = new Api\Login($this);
                break;
            case 'history':
                $api = new Api\History($this);
                break;
            case 'tags':
                $api = new Api\Tags($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * @param PocketRadarCredentialInterface $credential
     *
     * @param bool $getToken
     *
     * @throws PocketRadarClientException
     */
    public function setCredential(PocketRadarCredentialInterface $credential, $getToken = true)
    {
        $this->credential = $credential;
        //Set the access token
        $this->setAccessToken($credential->getAccessToken(), $getToken);
    }


    /**
     * Get the Access Token.
     *
     * @return string Access Token
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the Access Token.
     *
     * @param string $accessToken Access Token
     * @param bool $getToken
     *
     * @return PocketRadarClient
     * @throws PocketRadarClientException
     */
    public function setAccessToken(string $accessToken = null, $getToken = true)
    {
        if ($getToken && !$accessToken) {
            $response = $this->login()->session(
                $this->credential->getLogin(),
                $this->credential->getPassword()
            );

            if (!$response->hasError()) {
                $accessToken = $response->getDecodedBody()['token'];
            }
        }

        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the HTTP Client
     *
     * @return PocketRadarHttpClientInterface $httpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the HTTP Client
     *
     * @param PocketRadarHttpClientInterface $httpClient
     *
     * @return PocketRadarClient
     */
    public function setHttpClient(PocketRadarHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Get the API Base Path.
     *
     * @return string API Base Path
     */
    public function getBasePath()
    {
        return sprintf('%s/%s', static::BASE_PATH, $this->apiVersion);
    }

    /**
     * Get the Authorization Header with the Access Token.
     *
     * @param string $accessToken Access Token
     *
     * @return array Authorization Header
     */
    protected function buildAuthHeader($accessToken = "")
    {
        return ['x-auth-token' => $accessToken];
    }

    /**
     * Get the Content Type Header.
     *
     * @param string $contentType Request Content Type
     *
     * @return array Content Type Header
     */
    protected function buildContentTypeHeader($contentType = "")
    {
        return ['Content-Type' => $contentType];
    }

    /**
     * Build accept header
     *
     * @param string $accept
     *
     * @return array
     */
    protected function buildAcceptHeader($accept = "")
    {
        return ['Accept' => $accept];
    }

    /**
     * Make Request to the API
     *
     * @param string $method HTTP Request Method
     * @param string $endpoint API Endpoint to send Request to
     * @param array $params Request Query Params
     * @param array $options Request options
     * @param array $headers
     * @param string $accessToken Access Token to send with the Request
     *
     * @return PocketRadarResponse
     * @throws PocketRadarClientException
     */
    public function send($method, $endpoint, array $params = [], array $options = [], array $headers = [], $accessToken = null)
    {
        //Access Token
        $accessToken = $accessToken ?: $this->getAccessToken();
        //Make a PocketRadarRequest object
        $request = new PocketRadarRequest($method, $endpoint, $accessToken, $params, $headers);
        //Fetch and return the Response
        return $this->sendRequest($request, $options);
    }


    /**
     * Build URL for the Request
     *
     * @param string $endpoint Relative API endpoint
     *
     * @return string The Full URL to the API Endpoints
     */
    protected function buildUrl($endpoint = '')
    {
        //Get the base path
        $base = $this->getBasePath();

        //Join and return the base and api path/endpoint
        return sprintf('%s/%s', $base, $endpoint);
    }

    /**
     * Send the Request to the Server and return the Response
     *
     * @param PocketRadarRequest $request
     * @param array $options
     *
     * @return PocketRadarResponse
     *
     * @throws PocketRadarClientException
     */
    protected function sendRequest(PocketRadarRequest $request, array $options = [])
    {
        //Method
        $method = $request->getMethod();
        //Prepare Request
        list($url, $headers, $requestBody) = $this->prepareRequest($request);

        //Send the Request to the Server through the HTTP Client
        $rawResponse = $this->getHttpClient()->send($url, $method, $requestBody, $headers, $options);

        $response = new PocketRadarResponse($request);
        $response->setHttpStatusCode($rawResponse->getHttpResponseCode());
        $response->setHeaders($rawResponse->getHeaders());
        $response->setBody($rawResponse->getBody());

        return $response;
    }

    /**
     * Prepare a Request before being sent to the HTTP Client
     *
     * @param PocketRadarRequest $request
     *
     * @return array [Request URL, Request Headers, Request Body]
     */
    protected function prepareRequest(PocketRadarRequest $request)
    {
        //Build URL
        $url = $this->buildUrl($request->getEndpoint());
        //Request Body (Parameters)
        $requestBody = $request->getBody();
        //Empty body
        if (is_null($requestBody)) {
            //Content Type needs to be kept empty
            $request->setContentType("");
        }
        //Build headers
        $headers = array_merge(
            $this->buildAuthHeader($request->getAccessToken()),
            $this->buildContentTypeHeader($request->getContentType()),
            $this->buildAcceptHeader($request->getAccept()),
            $request->getHeaders()
        );
        //Return the URL, Headers and Request Body
        return [$url, $headers, $requestBody];
    }

    /**
     * @param string $name
     *
     * @param $args
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }
}
