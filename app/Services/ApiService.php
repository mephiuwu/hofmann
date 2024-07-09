<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://test.drogueriahofmann.cl/usuarios/',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getListTableUsers()
    {
        $response = $this->client->get('ListTableUsers');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUsers()
    {
        $response = $this->client->get('GetUsers');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function sendUser($data)
    {
        try {
            $response = $this->client->post('SendUser', [
                'json' => $data
            ]);
    
            $statusCode = $response->getStatusCode();
            return $statusCode;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            return $statusCode;
        }
        
    }
}
