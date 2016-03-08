<?php

namespace Afosto\ActiveAnts;

class Order extends Model {

    /**
     * The key used to lookup the model at ActiveAnts
     */
    protected $primaryKey = 'ExternalOrderNumber';

    /**
     * The ordernumber as known in third party software
     * @var string
     */
    public $ExternalOrderNumber;

    /**
     * The active ants order number
     * @var integer
     */
    public $OrderNumber;

    /**
     * Emailaddress for customer
     * @var string
     */
    public $Email;

    /**
     * The customers phonenumber
     * @var string
     */
    public $PhoneNumber;

    /**
     * Customers preferred shipping date 
     * yyyy-mm-dd
     * @var date
     */
    public $PreferredShippingDate;

    /**
     * The mapped value from the settings
     * @var integer
     */
    public $LanguageId;

    /**
     * The mapped value from the settings
     * @var integer
     */
    public $OrderTypeId;

    /**
     * The mapped value from the settings
     * @var integer
     */
    public $PaymentMethodId;

    /**
     * The mapped value from the settings
     * @var integer
     */
    public $ShippingMethodId;

    /**
     * Contains one or more order items
     * @var OrderItem[]
     */
    public $OrderItems = array();

    /**
     * Address for billing
     * @var Address
     */
    private $billingAddress;

    /**
     * Address for shipping
     * @var Address
     */
    private $shippingAddress;

    /**
     * The pickup point data
     * @var string
     */
    public $PickUpPointPostalCode, $PickUpPointHouseNumber, $PickUpPointAddition, $PickUpPointId;

    /**
     * Billing data
     * @var string
     */
    public $BillingAddressFirstName, $BillingAddressLastName, $BillingAddressPostalCode,
            $BillingAddressStreet, $BillingAddressHouseNumber, $BillingAddressHouseNumberAddition,
            $BillingAddressCityName, $BillingAddressCountryIso;

    /**
     * Delivery data
     * @var string
     */
    public $DeliveryAddressFirstName, $DeliveryAddressLastName, $DeliveryAddressPostalCode,
            $DeliveryAddressStreet, $DeliveryAddressHouseNumber, $DeliveryAddressHouseNumberAddition,
            $DeliveryAddressCityName, $DeliveryAddressCountryIso;

    /**
     * Build the default order
     */
    public function __construct() {
        //Make sure the default required settings are set
        $this->setOrderType()->setPaymentMethod()->setLanguage();
    }

    /**
     * The customer's email
     * @param string $email
     * @return \Afosto\ActiveAnts\Order
     */
    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->Email = $email;
        } else {
            throw new ApiException('Emailaddress invalid');
        }
        return $this;
    }

    /**
     * The third party orderId
     * @param string $orderId
     * @return \Afosto\ActiveAnts\Order
     */
    public function setOrderId($orderId) {
        if (strlen($orderId) < 3) {
            throw new ApiException('OrderId should at least have 3 chars');
        }
        $this->ExternalOrderNumber = $orderId;
        return $this;
    }

    /**
     * The customer's phoneNumber
     * @param string $phoneNumber
     * @return \Afosto\ActiveAnts\Order
     */
    public function setPhoneNumber($phoneNumber) {
        $this->PhoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Set the billing address
     * @param \Afosto\ActiveAnts\Address $address
     * @return \Afosto\ActiveAnts\Order
     */
    public function setBillingAddress(Address $address) {
        $this->billingAddress = $address;
        return $this;
    }

    /**
     * Set the shipping address
     * @param \Afosto\ActiveAnts\Address $address
     * @return \Afosto\ActiveAnts\Order
     */
    public function setShippingAddress(Address $address) {
        $this->shippingAddress = $address;
        return $this;
    }

    /**
     * Set the preferred shipping date
     * @param \DateTime $date
     * @return \Afosto\ActiveAnts\Order
     */
    public function setPreferredShippingDate(\DateTime $date = null) {
        if (is_null($date)) {
            $date = new \DateTime('NOW');
        }
        $this->PreferredShippingDate = date('Y-m-d', $date->getTimestamp());
        return $this;
    }

    /**
     * Add pickup point information
     * @param string $pointId
     * @param string $postalCode
     * @param integer $houseNumber
     * @param string $houseNumberAddition
     */
    public function setPickupPoint($pointId, $postalCode, $street) {
        $this->PickUpPointId = $pointId;
        $this->PickUpPointPostalCode = $postalCode;
        
        //Get the address data based on the street data
        $address = Address::getAddressFromStreet($street);
        $this->PickUpPointHouseNumber = $address->houseNumber;
        $this->PickUpPointAddition = $address->houseNumberAddition;        
    }

    /**
     * Add an item to the order
     * @param \Afosto\ActiveAnts\OrderItem $item
     * @return \Afosto\ActiveAnts\Order
     */
    public function addOrderItem(OrderItem $item) {
        $product = Product::model();
        $product->sku = $item->sku;
        if ($product->isNewRecord()) {
            throw new ApiException('Product does not exist');
        }
        array_push($this->OrderItems, $item);

        return $this;
    }

    /**
     * Set the customer language
     * @param string $code      2 letter isocode
     * @return \Afosto\ActiveAnts\Order
     */
    public function setLanguage($code = 'NL') {
        foreach (App::getInstance()->getSettings()->languages as $language) {
            if ($language->code == $code) {
                $this->LanguageId = $language->id;
                return $this;
            }
        }
        throw new ApiException('Invalid language');
    }

    /**
     * Select the default order type
     * @return \Afosto\ActiveAnts\Order
     */
    public function setOrderType($code = 'webwinkel_orders') {
        foreach (App::getInstance()->getSettings()->orderTypes as $orderType) {
            if ($orderType->code == $code) {
                $this->OrderTypeId = $orderType->id;
                return $this;
            }
        }
        throw new ApiException('Invalid order type');
    }

    /**
     * Set the payment method
     * @param type $code
     * @return \Afosto\ActiveAnts\Order
     */
    public function setPaymentMethod($code = 'GI') {
        foreach (App::getInstance()->getSettings()->paymentMethods as $paymentMethod) {
            if ($paymentMethod->code == $code) {
                $this->PaymentMethodId = $paymentMethod->id;
                return $this;
            }
        }
        throw new ApiException('Invalid payment method');
    }

    /**
     * Set the shipping method
     * @param string $code
     * @return \Afosto\ActiveAnts\Order
     */
    public function setShippingMethod($code = 'BUSEU1') {
        foreach (App::getInstance()->getSettings()->shippingMethods as $shippingMethod) {
            if ($shippingMethod->code == $code) {
                $this->ShippingMethodId = $shippingMethod->id;
                return $this;
            }
        }
        throw new ApiException('Invalid shipping method');
    }

    /**
     * Save the model
     * @param string $modelName
     */
    public function save() {
        if (!$this->isNewRecord()) {
            $this->message = 'Failed, order allready exists';
            return false;
        }
        //Merge the address data into this model
        $this->setAttributes($this->billingAddress->getAddress('BillingAddress'));
        $this->setAttributes($this->shippingAddress->getAddress('DeliveryAddress'));
        return parent::save();
    }

    /**
     * Return all data from api 
     * @param string $attributes
     * @return array
     */
    public function findAll($attributes = array()) {
        $data = $this->getData($attributes);
        $this->OrderNumber = $data[0];
        return array($this);
    }

}
