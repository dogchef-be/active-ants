<?php

namespace Afosto\ActiveAnts;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client {

    /**
     * The api token
     * @var string
     */
    private $token;

    /**
     * The guzzle client
     * @var GuzzleClient
     */
    private $client;

    /**
     * The username
     * @var string
     */
    private $username;

    /**
     * The password
     * @var string
     */
    private $password;

    /**
     * Additional headers to be send along the request
     * @var array
     */
    private $additionalHeaders;

    /**
     * The result
     * @var Response
     */
    private $response;

    /**
     * Set the username
     * @param string $username
     * @return \Afosto\ActiveAnts\ApiClient
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * Set the password
     * @param string $password
     * @return \Afosto\ActiveAnts\ApiClient
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Authorize to get a token
     * @param string $username
     * @param string $password
     * @return \Afosto\ActiveAnts\ApiClient
     */
    public function authorize() {
        if (App::getInstance()->cache->getCache('token')) {
            $this->token = App::getInstance()->cache->getCache('token');
        }
        if (is_null($this->token)) {
            $this->request('token', array(
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password
            ));
            $this->token = 'Bearer ' . $this->response->getParam('access_token');
            App::getInstance()->cache->setCache('token', $this->token, $this->response->getParam('expires_in'));
        }
        return $this;
    }

    /**
     * Send a request
     * @param string $path
     * @param array $data       Optional
     * @param string $method    Optional, autoselects based on the fact if data is empty or not
     * @return Response
     */
    public function request($path, $data = array(), $method = null) {
        $this->reset();
        if (!empty($data)) {
            $this->addHeaders(array(
                'form_params' => $data
            ));
        }
        try {
            $this->response = new Response($this->getGuzzleClient()->request($this->getMethod($data, $method), $path, $this->additionalHeaders));
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
        }
        return $this->response;
    }
    
    /**
     * Set the params for the request
     * @param type $params
     */
    private function addHeaders($params) {
        $this->additionalHeaders = array_merge_recursive($this->additionalHeaders, $this->removeEmpty($params));
    }

    /**
     * Reset for each request
     */
    private function reset() {
        $this->additionalHeaders = array();
        $this->response = null;
        $this->addHeaders(array(
            'headers' => array(
                'Authorization' => $this->token
        )));
    }

    /**
     * Return the appropriate method
     * @param array $data
     * @param string $method
     * @return string
     */
    private function getMethod($data, $method) {
        if (is_null($method)) {
            if (!empty($data)) {
                return 'POST';
            } else {
                return 'GET';
            }
        }
        return $method;
    }

    /**
     * Remove empty values from array
     * @param array $params
     * @return array
     */
    private function removeEmpty($params) {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = $this->removeEmpty($params[$key]);
            }
            if (empty($params[$key])) {
                unset($params[$key]);
            }
        }
        return $params;
    }

    /**
     * Return a guzzle client instance
     * @return GuzzleClient
     */
    private function getGuzzleClient() {
        if (is_null($this->client)) {
            $this->client = new GuzzleClient(array(
                'base_uri' => $this->getEndpoint(),
                'defaults' => array(
                    'headers' => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Client' => 'Afosto API Client'
                    )
                ),
                'allow_redirects' => false,
                'connect_timeout' => 10,
                'timeout' => 30
            ));
        }
        return $this->client;
    }

    /**
     * Return the endpoint for this api
     * @return string
     */
    private function getEndpoint() {
        return App::getInstance()->endpoint;
    }

}
