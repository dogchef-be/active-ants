<?php

namespace Afosto\ActiveAnts;

class Shipment extends Model {

    /**
     * The key used to lookup the model at ActiveAnts
     */
    protected $primaryKey = 'externalOrderNumber';

    /**
     * The id for the shipment
     * @var integer
     */
    public $Id;

    /**
     * The external order number (given from your application)
     * @var string
     */
    public $ExternalOrderNumber;

    /**
     * The shipping date, the day the package(s) are sent
     * @var string
     */
    public $ShippingDate;

    /**
     * Deprecated, not always set
     * @var string
     */
    public $TrackingNumber;

    /**
     * The number of packages
     * @var integer
     */
    public $NumberOfColli;

    /**
     * The amount of products for this shipment
     * @var integer
     */
    public $TotalNumberOfProducts;

    /**
     * The track and trace link (url to follow shipment)
     * @var string
     */
    public $TrackAndTraceCode;

}
