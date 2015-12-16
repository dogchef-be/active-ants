<?php

namespace Afosto\ActiveAnts;

class Product extends Model {

    /**
     * The key used to lookup the model at ActiveAnts
     * @var string
     */
    protected $primaryKey = 'sku';

    /**
     * Stockkeeping unit
     * @var string
     */
    public $sku;

    /**
     * The EAN for this product
     * @var string 
     */
    public $barcode;

    /**
     * Productname
     * @var string
     */
    public $name;

    /**
     * Product description, short description
     * @var string
     */
    public $description;

    /**
     * Set the SKU
     * @param string $sku
     * @param string $barcode
     * @return \Afosto\ActiveAnts\Product
     */
    public function setSku($sku, $barcode = null) {
        $this->sku = $sku;
        $this->barcode = $barcode;
        return $this;
    }
    
    /**
     * Set the products' descriptors
     * @param string $name
     * @param string $description
     * @return \Afosto\ActiveAnts\Product
     */
    public function setName($name, $description = null) {
        $this->name = $name;
        $this->description = $description;
        return $this;
    }
    
}
