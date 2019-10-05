<?php

namespace ImageBamAPI;

use Exception;
use ImageBamAPI\Abstracts\Request;

class RequestToken extends Request
{
    public function __construct(string $_apiKey = null, string $_apiSecret = null)
    {
        parent::__construct($_apiKey, $_apiSecret);
    }

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
    public function generateOAuthSignature(): string
    {
        $this->_oAuthSignature = md5(
            sprintf("%s%s%s%s", 
                $this->getAPIKey(),
                $this->getAPISecret(),
                $this->oAuthTimestamp,
                self::OAUTH_NONCE
            )
        );
        
        return $this->_oAuthSignature;
    }

    /**
     * Generate fields for request
     * All required data inside the object
     * 
     * @param array $newFields = []
     * 
     * @return array
     */
    public function generateFields(array $newFields = []): array
    {
        return [
            "oauth_consumer_key" => $this->getAPIKey(),
            "oauth_signature_method" => self::OAUTH_SIGNATURE_METHOD,
            "oauth_signature" => $this->generateOAuthSignature(),
            "oauth_timestamp" => $this->oAuthTimestamp,
            "oauth_nonce" => self::OAUTH_NONCE
        ];
    }

    /**
     * Execute oAuthRequest method
     * 
     * @return array
     */
    public function runRequestToken(): array
    {
        $token = $this->oAuthRequest(
            'http://www.imagebam.com/sys/oauth/request_token',
            $this->generateFields()
        );

        if($token[0] === false) throw new Exception("Failed while getting token data: " . $token[1], 503);
        
        $token = explode('&', $token[1]);
        $results = [];

        foreach($token as $row) {
            $var = explode('=', $row);
            $results[$var[0]] = $var[1];
        }

        return array_merge($results, [
            'oauth_timestamp' => $this->oAuthTimestamp,
            'oauth_signature' => $this->_oAuthSignature
        ]);
    }
}
