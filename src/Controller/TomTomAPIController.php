<?php
// src/Controller/TomTomAPIController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TomTomAPIController extends AbstractController
{
    /**
     * @Route("/TomTomAPI/{adresse}", name="TomTomAPI")
     */
    public function fetchGeoBias(HttpClientInterface $client, $adresse): Response
    {
        // Utilisez l'adresse passée en paramètre pour effectuer une requête à l'API TomTom
        $url = 'https://api.tomtom.com/search/2/geocode/' . urlencode($adresse) . '.json?key=4MAuSlFHkb1GSUsAaVzwArIAChF8W3Gf';
        $response = $client->request('GET', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->toArray();

        if ($statusCode === 200) {
            $latitude = $content['results'][0]['position']['lat'];
            $longitude = $content['results'][0]['position']['lon'];

            // Construire l'URL Google Maps avec les coordonnées de latitude et de longitude et le marqueur
            $googleMapsUrl = 'http://maps.google.com/?q=' . $latitude . ',' . $longitude . '&z=15';

            // Rediriger l'utilisateur vers l'URL Google Maps
            return $this->redirect($googleMapsUrl);
        }

        // Gérez les autres codes d'état si nécessaire
    }
}





