<?php

namespace App\Service;

use GuzzleHttp\Client;

class HttpClient
{
    public function get(string $url): string
    {
        $client = new Client([
            'timeout'  => 7.0,
        ]);

        $response = $client->get($url, ['verify' => false]);

        return $response->getBody()->getContents();
    }
}