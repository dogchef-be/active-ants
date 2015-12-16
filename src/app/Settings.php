<?php

namespace Afosto\ActiveAnts;

class Settings {

    /**
     * The shipping methods
     * @var array
     */
    public $shippingMethods;

    /**
     * The available payment methods
     * @var array
     */
    public $paymentMethods;

    /**
     * Ordertypes available
     * @var array
     */
    public $orderTypes;

    /**
     * Countries for shipping
     * @var array
     */
    public $countries;

    /**
     * Interfacing language (send along with the order)
     * @var array
     */
    public $languages;

    /**
     * Get a model
     * @return \self
     */
    public static function load() {
        return new self();
    }

    /**
     * Get the settings
     */
    public function __construct() {
        if (!$data = App::getInstance()->cache->getCache('settings')) {
            $data = (array) App::getInstance()->client->request('settings/get')->result;
            //Cache the settings for 24 hours as Active Ants requires
            App::getInstance()->cache->setCache('settings', $data, 60 * 60 * 24);
        }
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

}
