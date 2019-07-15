<?php

namespace App\Services\PocketRadar;

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;

class PocketRadarResponse
{
    /**
     *  The HTTP status code response
     *
     * @var int
     */
    protected $httpStatusCode;

    /**
     *  The headers returned
     *
     * @var array
     */
    protected $headers;

    /**
     *  The raw body of the response
     *
     * @var string
     */
    protected $body;

    /**
     *  The decoded body of the response
     *
     * @var array
     */
    protected $decodedBody = [];

    /**
     * The original request that returned this response
     *
     * @var PocketRadarRequest
     */
    protected $request;

    /**
     * Create a new PocketRadarResponse instance
     *
     * @param PocketRadarRequest $request
     * @param string|null $body
     * @param int|null $httpStatusCode
     * @param array $headers
     */
    public function __construct(PocketRadarRequest $request, $body = null, $httpStatusCode = null, array $headers = [])
    {
        $this->request = $request;
        $this->body = $body;
        $this->httpStatusCode = $httpStatusCode;
        $this->headers = $headers;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param int $httpStatusCode
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Get the Request Request
     *
     * @return PocketRadarRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the Response Body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the Decoded Body
     *
     * @return array
     * @throws PocketRadarClientException
     */
    public function getDecodedBody()
    {
        if (empty($this->decodedBody) || $this->decodedBody === null) {
            //Decode the Response Body
            $this->decodeBody();
        }

        return $this->decodedBody;
    }

    /**
     * Get Access Token for the Request
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->getRequest()->getAccessToken();
    }

    /**
     * Get Request Headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the HTTP Status Code
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Has error
     *
     * @return bool
     * @throws PocketRadarClientException
     */
    public function hasError()
    {
        $body = $this->getDecodedBody();

        return isset($body['error']) ? $body['error'] : false;
    }

    /**
     * Decode the Body
     *
     * @return void
     * @throws PocketRadarClientException
     *
     */
    protected function decodeBody()
    {
        $body = $this->getBody();

        if ($this->hasContentType('application/json')) {
            $this->decodedBody = (array)json_decode((string)$body, true);
        }

        // If the response needs to be validated
        if ($this->getRequest()->validateResponse()) {
            //Validate Response
            $this->validateResponse();
        }
    }

    /**
     * Validate Response
     *
     * @return void
     *
     * @throws PocketRadarClientException
     */
    protected function validateResponse()
    {
        // If JSON cannot be decoded
        if ($this->decodedBody === null) {
            throw new PocketRadarClientException("Invalid Response");
        }
    }

    /**
     * Has content tpe
     *
     * @param string $type
     *
     * @return bool
     */
    protected function hasContentType(string $type)
    {
        if (isset($this->headers['Content-Type'])) {
            foreach ($this->headers['Content-Type'] as $header) {
                $contentType = explode(';', $header)[0];
                if (trim($contentType) === $type) {
                    return true;
                }
            }
        }
        return false;
    }
}
