<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LanguageApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getLanguages(): array
    {
        $response = $this->client->request('GET', 'https://restcountries.com/v3.1/all');

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        $countries = $response->toArray();
        $languagesList = [];

        foreach ($countries as $country) {
            if (isset($country['languages'])) {
                foreach ($country['languages'] as $code => $langName) {
                    $languagesList[$langName] = $code;
                }
            }
        }

        // Supprimer les doublons et trier
        ksort($languagesList);

        return $languagesList;
    }
}
