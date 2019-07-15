<?php

namespace App\Services\PocketRadar;

/**
 * PocketRadarRequest
 */
class PocketRadarRequest
{

    /**
     * Access Token to use for this request
     *
     * @var string
     */
    protected $accessToken = null;

    /**
     * The HTTP method for this request
     *
     * @var string
     */
    protected $method = "GET";

    /**
     * The params for this request
     *
     * @var array
     */
    protected $params = null;

    /**
     * The Endpoint for this request
     *
     * @var string
     */
    protected $endpoint = null;

    /**
     * The headers to send with this request
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Content Type for the Request
     *
     * @var string
     */
    protected $contentType = 'application/x-www-form-urlencoded';

    /**
     * Accept header
     *
     * @var string
     */
    protected $accept = 'application/json';

    /**
     * If the Response needs to be validated
     * against being a valid JSON response.
     * Set this to false when an endpoint or
     * request has no return values.
     *
     * @var boolean
     */
    protected $validateResponse = true;


    /**
     * Create a new PocketRadarRequest instance
     *
     * @param string $method HTTP Method of the Request
     * @param string $endpoint API endpoint of the Request
     * @param string $accessToken Access Token for the Request
     * @param mixed $params Request Params
     * @param array $headers Headers to send along with the Request
     * @param null $contentType
     */
    public function __construct($method, $endpoint, $accessToken, array $params = [], array $headers = [], $contentType = null)
    {
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setAccessToken($accessToken);
        $this->setParams($params);
        $this->setHeaders($headers);

        if ($contentType) {
            $this->setContentType($contentType);
        }
    }

    /**
     * Get the Request Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the Request Method
     *
     * @param string
     *
     * @return PocketRadarRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get Access Token for the Request
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set Access Token for the Request
     *
     * @param string
     *
     * @return PocketRadarRequest
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the Endpoint of the Request
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the Endpoint of the Request
     *
     * @param string
     *
     * @return PocketRadarRequest
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the Content Type of the Request
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set the Content Type of the Request
     *
     * @param string
     *
     * @return PocketRadarRequest
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get the accept header of the Request
     *
     * @return string
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Set the accept header of the Request
     *
     * @param string
     *
     * @return PocketRadarRequest
     */
    public function setAccept($contentType)
    {
        $this->contentType = $contentType;

        return $this;
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
     * Set Request Headers
     *
     * @param array
     *
     * @return PocketRadarRequest
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Get JSON Encoded Request Body
     *
     * @return array
     */
    public function getBody()
    {
        $params = array_map(function($value) {
            if (!is_scalar($value)) {
                return json_encode($this->objectToArray($value));
            }

            return $value;
        }, $this->getParams());

        return http_build_query($params, '', '&');
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function objectToArray($object)
    {
        if(!is_object($object) && !is_array($object)) {
            return $object;
        }

        if(is_object($object)) {
            $object = get_object_vars($object);
        }

        return array_map([$this, 'objectToArray'], $object);
    }

    /**
     * Get the Request Params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the Request Params
     *
     * @param array
     *
     * @return PocketRadarRequest
     */
    public function setParams(array $params = [])
    {

        //Process Params
        $params = $this->processParams($params);

        //Set the params
        $this->params = $params;

        return $this;
    }

    /**
     * Whether to validate response or not
     *
     * @return boolean
     */
    public function validateResponse()
    {
        return $this->validateResponse;
    }

    /**
     * Process Params for the File parameter
     *
     * @param  array $params Request Params
     *
     * @return array
     */
    protected function processParams(array $params)
    {
        //Whether the response needs to be validated
        //against being a valid JSON response
        if (isset($params['validateResponse'])) {
            //Set the validateResponse
            $this->validateResponse = $params['validateResponse'];
            //Remove the validateResponse from the params array
            unset($params['validateResponse']);
        }

        return $params;
    }
}
