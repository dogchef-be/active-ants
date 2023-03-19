<?php

namespace ActiveAnts;

class Order extends Model
{
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
     * @var int
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
     * @var int
     */
    public $LanguageId;

    /**
     * The mapped value from the settings
     * @var int
     */
    public $OrderTypeId;

    /**
     * The mapped value from the settings
     * @var int
     */
    public $PaymentMethodId;

    /**
     * The mapped value from the settings
     * @var int
     */
    public $ShippingMethodId;

    /**
     * Contains one or more order items
     * @var OrderItem[]
     */
    public $OrderItems = [];

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
    public $PickUpPointPostalCode;

    public $PickUpPointHouseNumber;

    public $PickUpPointAddition;

    public $PickUpPointId;

    /**
     * Billing data
     * @var string
     */
    public $BillingAddressFirstName;

    public $BillingAddressLastName;

    public $BillingAddressPostalCode;

    public $BillingAddressStreet;

    public $BillingAddressHouseNumber;

    public $BillingAddressHouseNumberAddition;

    public $BillingAddressCityName;

    public $BillingAddressCountryIso;

    /**
     * Delivery data
     * @var string
     */
    public $DeliveryAddressFirstName;

    public $DeliveryAddressLastName;

    public $DeliveryAddressPostalCode;

    public $DeliveryAddressStreet;

    public $DeliveryAddressHouseNumber;

    public $DeliveryAddressHouseNumberAddition;

    public $DeliveryAddressCityName;

    public $DeliveryAddressCountryIso;

    /**
     * Build the default order
     */
    public function __construct()
    {
        //Make sure the default required settings are set
        //$this->setOrderType()->setPaymentMethod()->setLanguage();
    }

    /**
     * The customer's email
     * @param  string            $email
     * @return \ActiveAnts\Order
     */
    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->Email = $email;
        } else {
            throw new ApiException('Emailaddress invalid');
        }

        return $this;
    }

    /**
     * The third party orderId
     * @param  string            $orderId
     * @return \ActiveAnts\Order
     */
    public function setOrderId($orderId)
    {
        if (strlen($orderId) < 3) {
            throw new ApiException('OrderId should at least have 3 chars');
        }
        $this->ExternalOrderNumber = $orderId;

        return $this;
    }

    /**
     * The customer's phoneNumber
     * @param  string            $phoneNumber
     * @return \ActiveAnts\Order
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->PhoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Set the billing address
     * @param  \ActiveAnts\Address $address
     * @return \ActiveAnts\Order
     */
    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;

        return $this;
    }

    /**
     * Set the shipping address
     * @param  \ActiveAnts\Address $address
     * @return \ActiveAnts\Order
     */
    public function setShippingAddress(Address $address = null)
    {
        if (is_null($address)) {
            if (! empty($this->billingAddress)) {
                $this->shippingAddress = clone $this->billingAddress;
            } else {
                throw new ApiException('Cannot copy billing address as it is empty');
            }
        } else {
            $this->shippingAddress = $address;
        }

        return $this;
    }

    /**
     * Set the preferred shipping date
     * @param  \DateTime         $date
     * @return \ActiveAnts\Order
     */
    public function setPreferredShippingDate(\DateTime $date = null)
    {
        if (is_null($date)) {
            $date = new \DateTime('NOW');
        }
        $this->PreferredShippingDate = date('Y-m-d H:i:s', $date->getTimestamp());

        return $this;
    }

    /**
     * Add pickup point information
     * @param string $pointId
     * @param string $postalCode
     * @param string $street
     * @param string $city
     */
    public function setPickupPoint($pointId, $postalCode, $street, $city)
    {
        $this->PickUpPointId = $pointId;
        $this->PickUpPointPostalCode = $postalCode;

        //Get the address data based on the street data
        $pointAddress = Address::formatAddress($street, $postalCode, $city);
        $this->PickUpPointHouseNumber = $pointAddress->houseNumber;
        $this->PickUpPointAddition = $pointAddress->houseNumberAddition;

        //Overwrite the shipping to address to the values from the pickup point
        $this->shippingAddress->street = $pointAddress->street;
        $this->shippingAddress->houseNumber = $pointAddress->houseNumber;
        $this->shippingAddress->houseNumberAddition = $pointAddress->houseNumberAddition;
        $this->shippingAddress->cityName = $pointAddress->cityName;
        $this->shippingAddress->postalCode = $pointAddress->postalCode;

        //Set the extraname, only used for pickup points
        $this->shippingAddress->setExtraName();
    }

    /**
     * Add an item to the order
     * @param  \ActiveAnts\OrderItem $item
     * @return \ActiveAnts\Order
     */
    public function addOrderItem(OrderItem $item)
    {
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
     * @param  string            $code 2 letter isocode
     * @return \ActiveAnts\Order
     */
    public function setLanguage($code = 'NL')
    {
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
     * @return \ActiveAnts\Order
     */
    public function setOrderType($code = 'webwinkelorders_nl')
    {
        foreach (App::getInstance()->getSettings()->orderTypes as $orderType) {
            if ($orderType->code == $code) {
                $this->OrderTypeId = $orderType->id;

                return $this;
            }
        }

        throw new ApiException('Invalid order type', 400);
    }

    /**
     * Set the payment method
     * @param  type              $code
     * @return \ActiveAnts\Order
     */
    public function setPaymentMethod($code = 'GI')
    {
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
     * @param  string            $code
     * @return \ActiveAnts\Order
     */
    public function setShippingMethod($code = 'BUSEU1')
    {
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
    public function save()
    {
        if (! $this->isNewRecord()) {
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
     * @param  string $attributes
     * @return array
     */
    public function findAll($attributes = [])
    {
        $data = $this->getData($attributes);
        $this->OrderNumber = $data[0];

        return [$this];
    }
}
