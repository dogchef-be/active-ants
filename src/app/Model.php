<?php

namespace ActiveAnts;

class Model
{

    /**
     * Return a new model
     * @return \static
     */
    public static function model()
    {
        return new static();
    }

    /**
     * The query response
     * @var array
     */
    protected $result;

    /**
     * The result message
     * @var string 
     */
    protected $message;

    /**
     * The model's primary key
     * Null when model has no search function, this way we will skipp the
     * isNewRecord check, which is mandatory for some models
     * @var boolean
     */
    protected $primaryKey = null;

    /**
     * The action called for searching
     */
    protected $findAction = 'search';

    /**
     * The request type
     * @var string
     */
    protected $findMethod = 'POST';

    /**
     * Set the models attributes
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    /**
     * Returns true when product exists at ActiveAnts
     * @param array $modelName
     * @return boolean
     */
    public function isNewRecord()
    {
        if (!is_null($this->primaryKey)) {
            $response = App::getInstance()->client->request($this->_getPath() . '/' . $this->findAction, array($this->primaryKey => $this->_getPrimaryKey()));
            return !$response->success;
        }
        return true;
    }

    /**
     * Return all data from api 
     * @param string $attributes
     * @return array
     */
    public function findAll($attributes = array())
    {
        $results = $this->getData($attributes);
        $data = array();
        foreach ($results as $result) {
            $name = get_called_class();
            $object = new $name;
            foreach ($result as $key => $value) {
                $object->$key = $value;
            }
            $data[] = $object;
        }
        return $data;
    }

    /**
     * Find by the primary key
     * @param string $pk
     * @return array
     */
    public function findByPk($pk)
    {
        $response = App::getInstance()->client->request($this->_getPath() . '/' . $this->findAction, array($this->primaryKey => $pk), $this->findMethod);
        $result = $response->result;
        if (count($result) == 1) {
            $name = get_called_class();
            $object = new $name;
            foreach ($result[0] as $key => $value) {
                //Fix the fact that for shipment data (only use case) properties
                //are camelcase (as expected) but for all the other models aren't
                $key = strtoupper(substr($key, 0, 1)) . substr($key, 1);
                $object->$key = $value;
            }
            return $object;
        }
    }

    /**
     * Return the data from the api
     * @param array $attributes
     * @return array
     */
    protected function getData($attributes = array())
    {
        $response = App::getInstance()->client->request($this->_getPath() . '/' . $this->findAction, $attributes, $this->findMethod);
        if (!$response->success) {
            return array();
        }
        $result = $response->result;
        if (count($result) == 1) {
            return array($result);
        }
        return $result;
    }

    /**
     * Save the model
     * @param string $modelName
     * @return boolean
     */
    public function save()
    {
        if ($this->isNewRecord()) {
            $response = App::getInstance()->client->request($this->_getPath() . '/add', (array) json_decode(json_encode($this)));
        } else {
            $response = App::getInstance()->client->request($this->_getPath() . '/edit', (array) json_decode(json_encode($this)));
        }
        $this->message = $response->message;
        $this->result = $response->result;
        return $response->success;
    }

    /**
     * Returns the response for the query
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns the message string
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return the contents of the search key
     * @return string
     */
    private function _getPrimaryKey()
    {
        $key = $this->primaryKey;
        return $this->$key;
    }

    /**
     * Return the models path
     * @return string
     */
    private function _getPath()
    {
        $classPath = explode("\\", get_called_class());
        return strtolower(end($classPath));
    }
}
