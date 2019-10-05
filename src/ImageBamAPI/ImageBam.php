<?php

namespace ImageBamAPI;

use ImageBamAPI\Abstracts\Request;
use ImageBamAPI\RequestToken;
use ImageBamAPI\RequestAccessToken;

class ImageBam extends Request
{
    protected $_requestTokenModel, $_requestAccessTokenModel;

    public $requestToken, $accessToken, $oAuthTimestamp;

    public const OAUTH_SIGNATURE_METHOD = 'MD5';


    public function __construct(string $_apiKey = null, string $_apiSecret = null)
    {
        parent::__construct($_apiKey, $_apiSecret);

        $this->setOAuthTimestamp();

        // make instance model
        $this->_requestTokenModel = new RequestToken($_apiKey, $_apiSecret);
        $this->_requestAccessTokenModel = new RequestAccessToken($_apiKey, $_apiSecret);

        // get authorization token
        $this->requestToken = $this->_requestTokenModel->runRequestToken();
        
        // set result authorization token
        $this->_requestAccessTokenModel->setOAuthToken(
            $this->requestToken['oauth_token']
        );
        $this->_requestAccessTokenModel->setOAuthTokenSecret(
            $this->requestToken['oauth_token_secret']
        );
    }

    /**
     * Generate oauth signature
     *
     * For all request
     * oauth_signature = MD5(API-key + API-secret + oauth_timestamp + oauth_nonce + oauth_token + oauth_token_secret)
     * 
     * @return string
     */
    public function generateOAuthSignature(): string
    {
        $this->_oAuthSignature = md5(
            sprintf('%s%s%s%s%s%s', 
                $this->getAPIKey(),
                $this->getAPISecret(),
                time(),
                self::OAUTH_NONCE,
                $this->getAccessToken('oauth_token'),
                $this->getAccessToken('oauth_token_secret')
            )
        );
        
        return $this->_oAuthSignature;
    }

    /**
     * Get request token
     * 
     * @param string $indexName
     * 
     * @return mixed
     */
    public function getRequestToken(string $indexName = null)
    {
        return $indexName ? $this->requestToken[$indexName] : $this->requestToken;
    }

    /**
     * Request authorized access token
     * 
     * @param string $verifier
     */
    public function requestAccessToken(string $verifier)
    {
        $this->_requestAccessTokenModel->setOAuthVerifier($verifier);
        $this->accessToken = $this->_requestAccessTokenModel->runRequestToken();
    }

    /**
     * Get The Access token Informations
     */
    public function getAccessToken(string $indexName = null)
    {
        return $indexName ? $this->accessToken[$indexName] : $this->accessToken;
    }

    /**
     * Get galleries data
     * 
     * @return mixed
     */
    public function getGalleries()
    {
        $galleries = $this->oAuthRequest(
            'http://www.imagebam.com/sys/API/resource/get_galleries',
            $this->generateFields([
                'oauth_token' => $this->getAccessToken('oauth_token')
            ])
        );

        if(!$galleries[0])
            throw new Exception("Failed getting gallery data: " . $galleries[1], 503);
        else
            return json_decode($galleries[1]);
    }

    /**
     * Get gallery images data
     * 
     * @param string $gid (Gallery ID)
     * 
     * @return mixed
     */
    public function getGalleryImages(string $gid)
    {
        $images = $this->oAuthRequest(
            'http://www.imagebam.com/sys/API/resource/get_gallery_images',
            $this->generateFields([
                'oauth_token' => $this->getAccessToken('oauth_token'),
                'gallery_id' => $gid,
            ])
        );

        if(!$images[0])
            throw new Exception("Failed getting images data: " . $images[1], 503);
        else
            return json_decode($images[1]);
    }

    /**
     * Get gallery images data
     * 
     * @param string $gid (Gallery ID)
     * 
     * @return mixed
     */
    public function createGallery(string $title = null, string $description = null)
    {
        $gallery = $this->oAuthRequest(
            'http://www.imagebam.com/sys/API/resource/create_gallery',
            $this->generateFields([
                'oauth_token' => $this->getAccessToken('oauth_token'),
                'title' => $title,
                'description' => $description
            ])
        );

        if(!$gallery[0])
            throw new Exception("Failed getting images data: " . $gallery[1], 503);
        else
            return json_decode($gallery[1]);
    }

    /**
     * Upload image
     * 
     * @param array $data
     * 
     * @return mixed
     */
    public function uploadImage(array $data)
    {
        $image = $this->oAuthRequest(
            'http://www.imagebam.com/sys/API/resource/upload_image',
            $this->generateFields(array_merge(
                ['oauth_token' => $this->getAccessToken('oauth_token')],
                $data
            )),
            [
                'Content-Type: multipart/form-data'
            ]
        );

        if(!$image[0])
            throw new Exception("Failed while uploading images: " . $image[1], 503);
        else
            return json_decode($image[1]);
    }

    public function __wakeup()
    {
        // initial timestamp
        $this->_requestAccessTokenModel->setOAuthTimestamp();
        $this->_requestTokenModel->setOAuthTimestamp();
        $this->setOAuthTimestamp();

        // set api key and secret
        $this->setAPIKey($this->_requestTokenModel->getAPIKey());
        $this->setAPISecret($this->_requestTokenModel->getAPISecret());

        // set initial fields
        $this->initialFields([
            'oauth_consumer_key' => $this->getAPIKey()
        ]);
    }

    public function __sleep()
    {
        return ['accessToken', 'requestToken', '_requestAccessTokenModel', '_requestTokenModel'];
    }
}