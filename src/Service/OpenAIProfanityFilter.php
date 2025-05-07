<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIProfanityFilter
{
    private const API_URL = 'https://api.openai.com/v1/moderations';

    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey,
        private string $model
    ) {}

    public function containsProfanity(string $text): bool
    {
        $response = $this->client->request(
            'POST',
            self::API_URL,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input' => $text,
                    'model' => $this->model
                ]
            ]
        );

        $data = $response->toArray();
        return $data['results'][0]['flagged'] ?? false;
    }
}