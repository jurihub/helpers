<?php

/*
* (c) Jurihub <info@jurihub.fr>
*/

namespace Jurihub\Helpers;

/**
* A simple class to interact with the AddressGouv API
*/
class AddressApiGouvHelper
{
    /**
     * Effectue l'appel API
     * @param {String} $q L'adresse en format fulltext á géolocaliser
     * @param {String} $zip Code postal
     * @return {JSON}
     */
    static public function callApi($q, $zip = null)
    {
        $http = new \GuzzleHttp\Client;
        $url = "https://api-adresse.data.gouv.fr/search/?q=".$q;

        if ($zip) {
            $url .= "&postcode=".urlencode(utf8_encode($zip));
        }

        $response = $http->get($url);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Récupere les coordonnées pour une adresse
     * @param {String | Array} $address L'adresse á géolocaliser
     *      Si Array :
     *          address : numéro et rue
     *          zip : code postal
     *          city : ville
     * @param {Bool} $first
     * @return {Array} Les informations découpées en : latitude, longitude, address, zip, city et fullAddress
     *      Si $first est true, uniquement un résultat est renvoyé avec la collection d'infos
     *      Si $first est false, un array est renvoyé avec les collections d'infos
     */
    static public function getCoordinates($address, $first = false)
    {
        $q = "";

        if (is_array($address)) {
            foreach (['address', 'zip', 'city'] as $field) {
                if (isset($address[$field]) && $address[$field]) {
                    $q .= $address[$field]." ";
                }
            }
        } else {
            $q = $address;
        }

        $q = urlencode(utf8_encode(trim($q)));
        $datas = self::callApi($q);
        $coordinates = self::handle($datas);

        if (!$first) { // renvoie tous les résultats
            return $coordinates;
        }

        // renvoie que le 1er résultat si adresse trouvée ou null sinon
        return (empty($coordinates)) ? null : array_shift($coordinates);
    }

    static public function handle($datas)
    {
        if (!isset($datas['features'])) {
            return [];
        }

        $nb_results = count($datas['features']);

        if ($nb_results == 0) {
            return [];
        } elseif ($nb_results > 1) {
            return self::prepareResults($datas['features']);
        }

        return [self::prepareResult($datas['features'][0])];
    }

    /**
     * Formate les données retournées par l'API
     */
    static public function prepareResult($result)
    {
        return [
            'latitude' => $result['geometry']['coordinates'][1],
            'longitude' => $result['geometry']['coordinates'][0],
            'address' => $result['properties']['name'],
            'zip' => $result['properties']['postcode'],
            'city' => $result['properties']['city'],
            'fullAddress' => $result['properties']['label'],
        ];
    }

    static public function prepareResults($results)
    {
        $out = [];

        foreach ($results as $result) {
            $out[] = self::prepareResult($result);
        }

        return $out;
    }
}
