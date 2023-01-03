<?php

namespace ActiveAnts;

class PurchaseOrder extends Model {
        
    /**
     * The order reference
     * @var string
     */
    public $Reference;
    
    /**
     * The items
     * @var array
     */
    public $ApiPurchaseOrderItemViewModels = array();
    
    /**
     * Pass a not along with the purchase order
     * @param string $note
     * @return \ActiveAnts\PurchaseOrder
     */
    public function addReference($note) {
        $this->Reference = $note;
        return $this;
    }

    /**
     * Add a purchase order item
     * @param string $sku
     * @param integer $quantity
     * @param \DateTime $deliveryDate
     * @return \ActiveAnts\PurchaseOrder
     */
    public function addItem($sku, $quantity, \DateTime $deliveryDate = null) {
        $product = Product::model();
        $product->sku = $sku;
        if ($product->isNewRecord()) {
            throw new ApiException('Product does not exist');
        }
        $item['Sku'] = $sku;
        $item['Quantity'] = (int)$quantity;
        if (!is_null($deliveryDate)) {
            $item['ExpectedDeliveryDate'] = date('Y-m-d', $deliveryDate->getTimestamp());
        }
        array_push($this->ApiPurchaseOrderItemViewModels, $item);
        return $this;
    }
    
}
