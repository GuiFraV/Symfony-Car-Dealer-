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

    // Méthode qui fait un call api et récupère les données météorologiques suivant les options dans la query
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

        // Variable qui stocke la réponse sous forme d'un json() 
        $content = $response->getContent();
        // Variable qui permet de décoder le json()
        $weatherData = json_decode($content, true);

        // Retourne l'array contenant les données météorologiques
        return $weatherData;
    }
}