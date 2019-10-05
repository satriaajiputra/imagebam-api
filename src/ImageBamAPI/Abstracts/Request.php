<?php

namespace ImageBamAPI\Abstracts;

use ImageBamAPI\Interfaces\RequestInterface;
use ImageBamAPI\Traits\OAuthRequest;

abstract class Request implements RequestInterface
{
    use OAuthRequest;
    
    private $_apiKey = '';
    private $_apiSecret = '';

    // oauth signature
    public $_oAuthSignature;

    // oauth token
    public $oAuthToken;

    // oauth token secret
    public $oAuthTokenSecret;

    // fields data
    public $fields = [];

    // timestamp
    public $oAuthTimestamp;

    // constant data
    public const OAUTH_NONCE = 'AADDFFGG';
    public const OAUTH_SIGNATURE_METHOD = 'MD5';

    public function __construct(string $_apiKey = null, string $_apiSecret = null)
    {
        if($_apiKey) $this->setAPIKey($_apiKey);
        if($_apiSecret) $this->setAPISecret($_apiSecret);
        
        $this->setOAuthTimestamp();
        $this->initialFields();
    }

    /**
     * Set timestamp
     */
    public function setOAuthTimestamp()
    {
        $this->oAuthTimestamp = time();
    }

    /**
     * Set OAuthToken
     * 
     * @param string $oAuthToken
     */
    public function setOAuthToken(string $oAuthToken)
    {
        $this->oAuthToken = $oAuthToken;
    }

    /**
     * Set OAuthTokenSecret
     * 
     * @param string $oAuthTokenSecret
     */
    public function setOAuthTokenSecret(string $oAuthTokenSecret)
    {
        $this->oAuthTokenSecret = $oAuthTokenSecret;
    }

    /**
     * Generate oauth signature
     * 
     * @return string
     */
    abstract public function generateOAuthSignature(): string;

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
        return array_merge($this->fields, $newFields);
    }

    /**
     * Set initial fields datas
     */
    public function initialFields(array $newFields = [])
    {
        $this->fields = array_merge([
            // parameters required by OAuth
            "oauth_consumer_key" => $this->getAPIKey(),
            "oauth_signature_method" => self::OAUTH_SIGNATURE_METHOD,
            "oauth_signature" => $this->generateOAuthSignature(),
            "oauth_timestamp" => $this->oAuthTimestamp,
            "oauth_nonce" => self::OAUTH_NONCE,
        ], $newFields);
    }

    /**
     * set api key
     * 
     * @param string $apiKey
     */
    public function setAPIKey(string $apiKey)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * Get api key
     * 
     * @return mixed
     */
    public function getAPIKey()
    {
        return $this->_apiKey;
    }

    /**
     * Set api secret
     * 
     * @param strigng $apiSecret
     */
    public function setAPISecret(string $apiSecret)
    {
        $this->_apiSecret = $apiSecret;
    }

    /**
     * Get api secret
     * 
     * @return mixed
     */
    public function getAPISecret()
    {
        return $this->_apiSecret;   
    }
}