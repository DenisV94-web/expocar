<?php

class CurlHandler {
    private $curl = '';

    public function __construct() {
        $this->curl = curl_init();
    }

    public function __destruct() {
        curl_close($this->curl);
    }

    public function exec($url, $method = 'GET', $post = array(), $headers = array('Content-Type: application/json'))
    {
        $params = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => 'UTF-8',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers
        );


        if ($auth != "") {
            $params[CURLOPT_USERPWD] = $auth;
        }
        
        if ($method == 'POST') {
            if (!count($post)) {
                throw new Exception('CurlHandler::exec: Отсутствуют аргументы');
            } else {
                $params[CURLOPT_POSTFIELDS] = $post;
            }
        }

        curl_setopt_array($this->curl, $params);
        $response = curl_exec($this->curl);

        return json_decode($response);
    }
}