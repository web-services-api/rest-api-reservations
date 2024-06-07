<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class MovieService {


    public function __construct() {
        try {
            $this->client = new \GuzzleHttp\Client();
            $this->isApiUp($this->client);
        } catch (\Exception $e) {
            throw new \Exception("Error while creating Guzzle client: " . $e->getMessage());
        }

    }

    public function isApiUp($client) {
        try {
            $response = $client->request('GET', env('API_MOVIE_URL').'/health');
            if ($response->getStatusCode() != 200) {
                throw new \Exception("Unexpected error: " . $response->getStatusCode());
                return false;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // throw new \Exception("Error while requesting: " . $e->getMessage());
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return true;
    }



    public function getById($id) {
        try {
            $response = $this->client->request('GET', env('API_MOVIE_URL').'/movies/'.$id);
    
            $response = json_decode($response->getBody()->getContents(), true);
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // throw new \Exception("Error while requesting: " . $e->getMessage());
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
        }
        
        return $response['data'];
    }

    public function getAll($perPage) {
        try {
            $response = $this->client->request('GET', env('API_MOVIE_URL').'/movies?per_page='.$perPage);
    
            $response = json_decode($response->getBody()->getContents(), true);
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // throw new \Exception("Error while requesting: " . $e->getMessage());
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
        }
        
        return $response['data'];
    }

}



?>
