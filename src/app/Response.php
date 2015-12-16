<?php

namespace Afosto\ActiveAnts;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

class Response {

    /**
     * The status
     * @var boolean
     */
    public $success = true;

    /**
     * The message
     * @var string 
     */
    public $message;
    
    /**
     * The response body
     * @var array
     */
    public $result;

    /**
     * Build the result
     * @param GuzzleResponse $response
     */
    public function __construct(GuzzleResponse $response) {
        $data = json_decode($response->getBody());
        if (isset($data->messageCode)) {
            $this->success = (($data->messageCode == 'OK') ? true : false);
            $this->message = $data->message;
            $this->result = $data->result;
        } else {
            $this->result = $data;
        }
    }

    /**
     * Return the param
     * @param string $key
     * @return string|array
     */
    public function getParam($key) {
        if (isset($this->result->$key)) {
            return $this->result->$key;
        }
        return null;
    }

}
