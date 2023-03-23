<?php

namespace ActiveAnts;

class Address extends Model
{

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
     * The 'extra' name, used for pickup points
     * @var string
     */
    public $extraName;

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
     * Funky code to get the housenumber and addition out of a street
     * The mashape API (used for DHL pickup points) only returns address data
     * @param srting $street Can be address (with housenumber)
     * @param string $postalCode
     * @param string $city
     * @param integer $houseNumber Optional
     * @param string $houseNumberAddition Optional
     * @return \ActiveAnts\Address
     */
    public static function formatAddress($street, $postalCode, $city, $houseNumber = null, $houseNumberAddition = null)
    {
        $address = new Address();
        if (is_null($houseNumber)) {
            //If housenumber was not provided, try to retreive it from the street (address)
            $pattern = '#^([a-z0-9 [:punct:]\']*) ([0-9]{1,5})([a-z0-9 \-/]{0,})$#i';
            preg_match($pattern, str_replace(' - ', '-', $street), $aMatch);
            $address->street = $aMatch[1];
            $address->houseNumber = preg_replace("/[^A-Za-z0-9 ]/", '', $aMatch[2]);
            if (isset($aMatch[3])) {
                $address->houseNumberAddition = preg_replace("/[^A-Za-z0-9 ]/", '', $aMatch[3]);
            }
        } else {
            $address->street = $street;
            $address->houseNumber = $houseNumber;
            $address->houseNumberAddition = $houseNumberAddition;
        }
        $address->postalCode = $postalCode;
        $address->cityName = $city;

        return $address;
    }

    /**
     * Set the country code based on the available settings
     * @param type $code
     * @return \ActiveAnts\Address
     * @throws ApiException
     */
    public function setCountry($code)
    {
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
     * @return \ActiveAnts\Address
     */
    public function setName($lastName, $firstName = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Set the address information
     * @param string $street
     * @param integer $number
     * @param string $addition
     * @return \ActiveAnts\Address
     */
    public function setAddress($street, $number, $addition = null)
    {
        $this->street = $street;

        $firstLetterPos = strcspn($number, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        if ($firstLetterPos !== strlen($number)) {
            $firstPart = substr($number, 0, $firstLetterPos);
            $secondPart = substr($number, $firstLetterPos);
            $secondPart = $addition === null ? $secondPart : $secondPart . " " . $addition;
        } else {
            $firstPart = $number;
            $secondPart = $addition;
        }
        $this->houseNumber = $firstPart;
        $this->houseNumberAddition = $secondPart;
        return $this;
    }

    /**
     * Set the postalcode information
     * @param string $postalCode
     * @return \ActiveAnts\Address
     */
    public function setPostalcode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Set the city
     * @param string $city
     * @return \ActiveAnts\Address
     */
    public function setCity($city)
    {
        $this->cityName = $city;
        return $this;
    }

    /**
     * Return the flattend data
     * @param string $type
     * @return array
     */
    public function getAddress($type)
    {
        $address = array();
        foreach (array_keys(get_class_vars(__CLASS__)) as $key) {
            $u = ucwords(substr($key, 0, 1)) . substr($key, 1);
            $address[$type . $u] = $this->$key;
        }
        return $address;
    }

    /**
     * Sets the 'extra' name
     */
    public function setExtraName()
    {
        $this->extraName = 'T.a.v.' . $this->firstName . ' ' . $this->lastName .
            ' (' . $this->getFullHouseNumber() . ')';
    }

    /**
     * Return the formatted housenumber
     * @return string
     */
    public function getFullHouseNumber()
    {
        if (is_null($this->houseNumberAddition) || trim($this->houseNumberAddition) == '') {
            return $this->houseNumber;
        }
        return $this->houseNumber . ' ' . $this->houseNumberAddition;
    }
}
