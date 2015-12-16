<?php

namespace Afosto\ActiveAnts;

class Address extends Model {

    /**
     * Customer first name
     * @var string
     */
    public $firstName;
    
    /**
     * Customer last name
     * @var string
     */
    public $lastName;
    
    /**
     * Postal code
     * @var string
     */
    public $postalCode;
    
    /**
     * The street
     * @var string
     */
    public $street;
    
    /**
     * The house number
     * @var string
     */
    public $houseNumber;
    
    /**
     * The house number addition
     * @var string
     */
    public $houseNumberAddition;
    
    /**
     * Cityname
     * @var string
     */
    public $cityName;
    
    /**
     * Country ISO
     * @var string
     */
    public $countryIso;
    
    /**
     * Set the country code based on the available settings
     * @param type $code
     * @return \Afosto\ActiveAnts\Address
     * @throws ApiException
     */
    public function setCountry($code) {
        foreach (App::getInstance()->getSettings()->countries as $country) {
            if ($country->code == $code) {
                $this->countryIso = $country->code;
                return $this;
            }
        }
        throw new ApiException('Invalid country code');
    }
    
    /**
     * Set the customer name
     * @param string $lastName
     * @param string $firstName
     * @return \Afosto\ActiveAnts\Address
     */
    public function setName($lastName, $firstName = null) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        return $this;
    }
    
    /**
     * Set the address information
     * @param string $street
     * @param integer $number
     * @param string $addition
     * @return \Afosto\ActiveAnts\Address
     */
    public function setAddress($street, $number, $addition = null) {
        $this->street = $street;
        $this->houseNumber = $number;
        $this->houseNumberAddition = $addition;
        return $this;
    }
    
    /**
     * Set the postalcode information
     * @param string $postalCode
     * @return \Afosto\ActiveAnts\Address
     */
    public function setPostalcode($postalCode) {
        $this->postalCode = $postalCode;
        return $this;
    }
    
    /**
     * Set the city
     * @param string $city
     * @return \Afosto\ActiveAnts\Address
     */
    public function setCity($city) {
        $this->cityName = $city;
        return $this;
    }
    
    /**
     * Return the flattend data
     * @param string $type
     * @return array
     */
    public function getAddress($type) {
        $address = array();
        foreach (array_keys(get_class_vars(__CLASS__)) as $key) {
            $u = ucwords(substr($key, 0,1)) . substr($key, 1);
            $address[$type . $u] = $this->$key;
        }
        return $address;
    }
    
    

}
