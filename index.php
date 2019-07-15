<?php

require 'vendor/autoload.php';

use App\Services\PocketRadar\Exceptions\PocketRadarClientException;
use App\Services\PocketRadar\PocketRadarClient;
use App\Services\PocketRadar\Http\Clients\PocketRadarGuzzleHttpClient;

$token = 'asdasdasdasdasd';

$httpClient = new PocketRadarGuzzleHttpClient();
$pocketRadar = new PocketRadarClient($httpClient);

try {
    $pocketRadar->setAccessToken($token);
    $response = $pocketRadar->tags()->getActivitiesTags();
    var_dump($response->getDecodedBody());
} catch (PocketRadarClientException $e) {
    echo $e->getMessage();
}
