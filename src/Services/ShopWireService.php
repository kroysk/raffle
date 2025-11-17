<?php

namespace App\Services;

use App\Config\Config;
use Exception;
class ShopWireService
{
    private string $apiUrl;
    public function __construct()
    {
        $this->apiUrl = Config::get('SHOPWIRED_API_URL', 'https://api.ecommerceapi.uk/v1');
    }

    public function request(string $endpoint, string $method = 'GET', array $data = [], $account = [])
    {
        $url = $this->apiUrl . $endpoint;
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'authorization' => 'Basic ' . base64_encode($account['shopwired_api_key'] . ':' . $account['shopwired_api_secret']),
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Basic " . base64_encode($account['shopwired_api_key'] . ':' . $account['shopwired_api_secret']),
            "content-type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        if ($httpCode === 401) {
            throw new Exception("Unauthorized: Probably Invalid API key or secret");
        }
       
        return [
            'status_code' => $httpCode,
            'body' => json_decode($response, true) ?? [],
        ];
    }

    public function createProduct(array $account, array $data)
    {
        return $this->request('/products', 'POST', $data, $account);
    }

    public function disableProduct(array $account, string $productId)
    {
        return $this->request('/products/' . $productId, 'PUT', ['active' => false], $account);
    }
}