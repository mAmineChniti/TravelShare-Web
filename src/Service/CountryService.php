<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCountries(): array
    {
        $response = $this->client->request('GET', 'https://restcountries.com/v3.1/all');
        $data = $response->toArray();

        $countries = [];
        foreach ($data as $country) {
            if (isset($country['name']['common'])) {
                $countries[$country['name']['common']] = $country['name']['common'];
            }
        }

        ksort($countries); // trie alphabétique

        return $countries;
    }
}
