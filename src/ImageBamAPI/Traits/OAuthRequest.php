<?php

namespace ImageBamAPI\Traits;

/**
 * OAuthRequest function
 */
trait OAuthRequest
{
    /**
     * Run oauth request
     * 
     * @param string $url
     * @param array $fields
     * @param array $headers
     * 
     * @return mixed
     */
    public function oAuthRequest(string $url, array $fields, array $headers = [])
    {
        // initialize curl and set parameters

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // execute, get information and close connection
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        // Check if all went ok
        if($info['http_code'] != 200) return [false, $response];
        
        return [true, $response];
    }
}
