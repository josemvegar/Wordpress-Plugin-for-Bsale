<?php
/**
 * Bsale Admin Clases plugin
 * @link        https://josecortesia.cl
 * @since       1.0.0
 * 
 * @package     base
 * @subpackage  base/include
 */

 /*require 'vendor/autoload.php';

class CurlRequestBsale {


    //private $token="a1db9545ead1ad4181370c635f1bbf556e04b5b0";
    private $headers=[
        "access_token: a1db9545ead1ad4181370c635f1bbf556e04b5b0",
        "Content-Type: application/json"
    ];

    private function makeRequest($url) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers,
        ));
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            curl_error($curl);
            curl_close($curl);
            return false;
        }else{
            curl_close($curl);
            return $response;
        }
    }

    public function buildProductsJson(){
        $products = json_decode($this->getProducts(), true);
        return json_encode($products, JSON_PRETTY_PRINT);
    }

    public function getProducts(){
        $counter = 0;
        $jsonProducts = [];

        // Realizar la solicitud inicial de productos
        $products = json_decode($this->makeRequest("https://api.bsale.cl/v1/products.json"), true);

        // Obtener detalles de variantes, stock y precios para cada producto
        $jsonProducts = array_map(function($product) {
            $variant = $this->getVariantsByProduct($product['id']);
            $stock = $this->getStockByVariant($variant["id"]);
            $price = $this->getPriceByVariant($variant["id"]);
            return [
                "id" => $product['id'],
                "name" => $product['name'],
                "variantId" => $variant["id"],
                "sku" => $variant["code"],
                "stock" => $stock,
                "price" => $price
            ];
        }, $products['items']);

        return json_encode($jsonProducts, JSON_PRETTY_PRINT);
    }

    private function getVariantsByProduct($id){
        $url = "https://api.bsale.cl/v1/products/$id/variants.json";
        $variants = json_decode($this->makeRequest($url), true);
        return $variants['items'][0];
    }

    private function getStockByVariant($id){
        $url = "https://api.bsale.cl/v1/stocks.json?variantid=$id";
        $stocks = json_decode($this->makeRequest($url), true);
        return $stocks["items"][0]["quantityAvailable"];
    }

    private function getPriceByVariant($id){
        $url = "https://api.bsale.cl/v1/price_lists/6/details.json?variantid=$id";
        $prices = json_decode($this->makeRequest($url), true);
        return $prices["items"][0]["variantValueWithTaxes"];
    }

}*/


use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class CurlRequestBsale {
    //private $headers = ['access_token' => 'a1db9545ead1ad4181370c635f1bbf556e04b5b0'];
    private $headers = ['access_token' => '1e8466ceb957ded197876fe675c1bec1f16d79e4'];
    private $tax;

    private $client;

    public function __construct() {
        $this->client = new Client();
        $this->tax = $this->getTaxBsale();
    }

    public function getTaxBsale() {
        $url= 'https://api.bsale.cl/v1/taxes/1.json';
        $response = $this->client->request('GET', $url, ['headers' => $this->headers]);
        $data = json_decode($response->getBody(), true);
        return $data["percentage"];
    }
    
    public function buildProductsJson() {
        $products = $this->getProducts();
        return json_encode($products, JSON_PRETTY_PRINT);
        die();
    }

    public function getProducts() {
        $products = [];
        $url = 'https://api.bsale.cl/v1/products.json?limit=50&expand[variants]';
    
        do {
            $response = $this->client->request('GET', $url, ['headers' => $this->headers]);
            $data = json_decode($response->getBody(), true);
            $products = array_merge($products, $data['items']);
            $url = isset($data['next']) ? $data['next'] . '&expand[variants]' : null;
        } while ($url);
        return $this->getVariantDetails($products);
    }

    private function getVariantDetails($products) {
        $variantDetails = [];
        foreach ($products as $product) {
            $response= $this->client->request('GET', "https://api.bsale.cl/v1/variants/{$product["variants"]["items"][0]["id"]}/costs.json", ['headers' => $this->headers]);
            $data=json_decode($response->getBody(), true);
            $variantDetails[] = [
                "id" => $product['id'],
                "name" => $product['name'],
                "variantId" => $product["variants"]["items"][0]["id"],
                "sku" => $product["variants"]["items"][0]["code"],
                "stock" => $data["history"][0]["availableFifo"],
                "price" => $data["history"][0]["cost"],
                "priceIva" => round($data["history"][0]["cost"] + (($data["history"][0]["cost"] * $this->tax)/100))

            ];
        }
        return $variantDetails;
    }

    /*public function getProducts() {
        $products = [];
        $url = 'https://api.bsale.cl/v1/products.json?limit=50&expand[variants]';
    
        do {
            $response = $this->client->request('GET', $url, ['headers' => $this->headers]);
            $data = json_decode($response->getBody(), true);
            $products = array_merge($products, $data['items']);
            $url = isset($data['next']) ? $data['next'] . '&expand[variants]' : null;
        } while ($url);
    
        return $this->getVariantDetails($products);
    }
    
    private function getVariantDetails($products) {
        $variantDetails = [];
        $promises = [];
    
        foreach ($products as $product) {
            $variantId = $product["variants"]["items"][0]["id"];
            $url = "https://api.bsale.cl/v1/variants/{$variantId}/costs.json";
            $promises[$variantId] = $this->client->requestAsync('GET', $url, ['headers' => $this->headers]);
        }
     
        $responses = Promise\settle($promises)->wait();
    
        foreach ($responses as $variantId => $response) {
            if ($response['state'] === 'fulfilled') {
                $data = json_decode($response['value']->getBody(), true);
                $variantDetails[] = [
                    "id" => $product['id'],
                    "name" => $product['name'],
                    "variantId" => $variantId,
                    "sku" => $product["variants"]["items"][0]["code"],
                    "stock" => $data["history"][0]["availableFifo"],
                    "price" => $data["history"][0]["cost"],
                    "priceIva" => round($data["history"][0]["cost"] + (($data["history"][0]["cost"] * $this->tax) / 100))
                ];
            }
        }
    
        return $variantDetails;
    }*/

    public function getProductsStocks() {
        $stocks = [];
        $url = 'https://api.bsale.cl/v1/stocks.json?limit=50&expand[variant]';
    
        do {
            $response = $this->client->request('GET', $url, ['headers' => $this->headers]);
            $data = json_decode($response->getBody(), true);
            foreach ($data["items"] as $stock) {
                $stocks[] = [
                    "sku" => $stock["variant"]["code"],
                    "stock" => $stock["quantityAvailable"]
                ];
            }
            $url = isset($data['next']) ? $data['next'] . '&expand[variant]' : null;
        } while ($url);
        return $stocks;
    }

    public function getPriceLists() {
        $prices = [];
        $url = 'https://api.bsale.cl/v1/price_lists/6/details.json?limit=50&expand[variant]';
    
        do {
            $response = $this->client->request('GET', $url, ['headers' => $this->headers]);
            $data = json_decode($response->getBody(), true);
            foreach ($data["items"] as $price) {
                $prices[] = [
                    "sku" => $price["variant"]["code"],
                    "price" => round($price["variantValue"]),
                    "priceIva" => $price["variantValueWithTaxes"],
                ];
            }
            $url = isset($data['next']) ? $data['next'] . '&expand[variant]' : null;
        } while ($url);
        return $prices;
    }
}