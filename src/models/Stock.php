<?php

namespace Afosto\ActiveAnts;

class Stock extends Model {

    /**
     * The stock keeping unit
     * @var string
     */
    public $sku;

    /**
     * The product name
     * @var string
     */
    public $name;

    /**
     * The stock count
     * @var integer
     */
    public $stock;

    /**
     * The stock uses a different action to find results
     * @var string
     */
    protected $findAction = 'bulk/true';

    /**
     * The stock uses a differnt method to find results
     * @var string
     */
    protected $findMethod = 'GET';

}
