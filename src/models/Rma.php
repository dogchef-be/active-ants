<?php

namespace ActiveAnts;

class Rma extends Model {

    /**
     * The RMA items
     * @var array
     */
    public $ApiRmaNotificationItemViewModels = array();

    /**
     * The originating third party order number
     * @var string 
     */
    public $ExternalOrderNumber;

    /**
     * 
     * @param type $sku
     * @param type $isDamaged
     * @param type $quantity
     * @return \ActiveAnts\Rma
     */
    public function addItem($sku, $isDamaged = false, $quantity = 1) {
        array_push($this->ApiRmaNotificationItemViewModels, array(
            'ProductSku' => $sku,
            'Quantity' => (int) $quantity,
            'IsDamaged' => ($isDamaged ? 1 : 0)
        ));
        return $this;
    }

    /**
     * Set the order id
     * @param \ActiveAnts\Order|string $order
     */
    public function setOrderId($order) {
        if ($order instanceof Order) {
            $this->ExternalOrderNumber = $order->ExternalOrderNumber;
        } else {
            $this->ExternalOrderNumber = $order;
        }
        return $this;
    }

}
