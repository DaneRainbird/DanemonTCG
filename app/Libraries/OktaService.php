<?php
    namespace App\Libraries;

    /**
     * OktaService
     * 
     * Helper service used to manage authentication with Okta
     * 
     * Written by Dane Rainbird (hello@danerainbird.me)
     */
    class OktaService {

        // Variable declarations 
        private $clientId;
        private $clientSecret;
        private $redirectUrl;
        private $metadataUrl;
        private $curl;

        /**
         * __construct
         * @return new instance of OktaService
         */
        public function __construct() {

            // Get Okta environment variables 
            $this->clientId = env('okta.clientId');
            $this->clientSecret = env('okta.clientSecret');
            $this->redirectUrl = env('okta.redirectUrl');
            $this->metadataUrl = env('okta.metadataUrl');

            // Initialise curl
            $this->curl = \Config\Services::curlrequest();

        }

        /**
         * buildAuthorizeUrl
         * 
         * Builds the URL used to initiate the Okta login process
         * 
         * @param string $state - state to be passed to Okta
         * @return string containing the authorization url
         */
        public function buildAuthorizeUrl($state) {
            
            // cURL request to get the authorization url
            $metadata = $this->curl->request('GET', $this->metadataUrl);

            // If successful, get the authorization url from the response
            if ($metadata->getStatusCode() == 200) {
                $metadata = json_decode($metadata->getBody());
                $authorizeUrl = $metadata->authorization_endpoint;
            } else {
                return 'Error: ' . $metadata->statusCode;
            }

            // Generate the authorization url
            $url = $metadata->authorization_endpoint . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUrl,
                'state' => $state,
                'scope' => 'openid email'
            ]);

            return $url;
        }

        /**
         * authorizeUser
         * 
         * Authorize the user with Okta and obtain a list of "claims"
         * 
         * @param string $state - the state returned from Okta
         * @return object containing result of the authorization
         */
        public function authorizeUser($state) {

            // If the state does not match, then return an error
            if ($state != $_GET['state']) {
                $result['error'] = true;
                $result['errorMessage'] = 'Authorization server returned an invalid state parameter';
                return $result;
            }
    
            // If an error is returned, then return an error
            if (isset($_GET['error'])) {
                $result['error'] = true;
                $result['errorMessage'] = 'Authorization server returned an error: '.htmlspecialchars($_GET['error']);
                return $result;
            }
    
            // Get metadata from Okta
            $metadata = $this->curl->request('GET', $this->metadataUrl);

            // Create request body in x-www-form-urlencoded format
            $requestBody = 'grant_type=authorization_code' . '&code=' . $_GET['code'] . '&redirect_uri=' . $this->redirectUrl;

            // Send a request to the token endpoint to get an access token
            $response = $this->curl->setBody($requestBody)->request('POST', json_decode($metadata->getBody())->token_endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'auth' => [$this->clientId, $this->clientSecret, 'basic'],
                'http_errors' => false
            ]);

            // If Okta does not return an id_token, then return an error
            if (!isset(json_decode($response->getBody())->id_token)) {
                $result['error'] = true;
                $result['errorMessage'] = 'Error fetching ID token!';
                return $result;
            }
    
            // Get the user's claims from the id_token            
            $claims = json_decode(base64_decode(explode('.', json_decode($response->getBody())->id_token)[1]));
            
            // Update results object with user's claims and a success value
            $result['username'] = $claims->email;
            $result['success'] = true;
            return $result;
        }
    }