<?php

namespace ActiveAnts;

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

    /**
     * Returns the updated stock items only
     * @return Stock[]
     */
    public function getUpdates() {
        //Change the find action to false to receive the updated records only
        $this->findAction = 'bulk/false';
        return $this->findAll();
    }
}
