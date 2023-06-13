<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getWeatherData(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.open-meteo.com/v1/forecast',
            [
                'query' => [
                    'latitude' => '48.85', 
                    'longitude' => '2.35',
                    'hourly' => 'temperature_2m', 
                ]
            ]
        );

        $content = $response->getContent();
        $weatherData = json_decode($content, true);

        return $weatherData;
    }
}