<?php

namespace Afosto\ActiveAnts;

class OrderItem extends Model {

    /**
     * The product name
     * @var string
     */
    public $name;

    /**
     * The product's sku
     * @var string
     */
    public $sku;

    /**
     * The price
     * @var float
     */
    public $price;

    /**
     * The amount of ordered items for this product
     * @var integer
     */
    public $quantity = 1;

    /**
     * The vat charges
     * @var float
     */
    public $vat;

    /**
     * Set the product for this order-row
     * @param string $sku
     * @return \Afosto\ActiveAnts\OrderItem
     */
    public function setSku($sku, $validate = true) {
        if ($validate && Product::model()->isNewRecord(array('sku' => $sku))) {
            throw new ApiException('Product not available at ActiveAnts');
        }
        $this->sku = $sku;
        return $this;
    }

    /**
     * Return the name
     * @param string $name
     * @return \Afosto\ActiveAnts\OrderItem
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the price
     * @param float $price
     * @return \Afosto\ActiveAnts\OrderItem
     */
    public function setGrossPrice($price) {
        $this->price = round(floatval($price), 2);
        return $this;
    }

    /**
     * Set the taxRate to calculate the VAT
     * @param integer $taxRate
     * @return \Afosto\ActiveAnts\OrderItem
     */
    public function setTaxRate($taxRate) {
        $this->vat = round($this->price - (($this->price * 100) / (100 + (int)$taxRate)), 2);
        return $this;
    }

    /**
     * The amount
     * @param integer $quantity
     * @return \Afosto\ActiveAnts\OrderItem
     */
    public function setQuantity($quantity) {
        $this->quantity = (int) $quantity;
        return $this;
    }

}
