<?php

namespace ImageBamAPI\Interfaces;

interface RequestInterface
{
    /**
     * Constract set default api public and secret
     * 
     * @param string $_apiKey
     * @param string $_apiSecret
     */
    public function __construct(string $_apiKey = null, string $_apiSecret = null);

    /**
     * set api key
     * 
     * @param string $apiKey
     */
    public function setAPIKey(string $apiKey);

    /**
     * Get api key
     * 
     * @return mixed
     */
    public function getAPIKey();

    /**
     * Set api secret
     * 
     * @param strigng $apiSecret
     */
    public function setAPISecret(string $apiSecret);

    /**
     * Get api secret
     * 
     * @return mixed
     */
    public function getAPISecret();

    /**
     * Set timestamp
     */
    public function setOAuthTimestamp();

    /**
     * Generate oauth signature
     * 
     * For requesting token
     * oauth_signature = MD5(API-key + API-secret + oauth_timestamp + oauth_nonce)
     * 
     * For all request
     * oauth_signature = MD5(API-key + API-secret + oauth_timestamp + oauth_nonce + oauth_token + oauth_token_secret)
     * 
     * @return string
     */
    public function generateOAuthSignature(): string;

    /**
     * Run oauth request
     * 
     * @param string $url
     * @param array $fields
     * 
     * @return mixed
     */
    public function oAuthRequest(string $url, array $fields);

    /**
     * Generate fields for request
     * All required data inside the object
     * 
     * @param array $newFields = []
     * 
     * @return array
     */
    public function generateFields(array $newFields = []): array;

    /**
     * Set initial fields datas
     */
    public function initialFields(array $newFields = []);
}
