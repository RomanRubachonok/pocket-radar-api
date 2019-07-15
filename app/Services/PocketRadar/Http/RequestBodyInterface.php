<?php
namespace App\Services\PocketRadar\Http;

use Psr\Http\Message\StreamInterface;

/**
 * RequestBodyInterface
 */
interface RequestBodyInterface
{
    /**
     * Get the Body of the Request
     *
     * @return string|resource|StreamInterface
     */
    public function getBody();
}
