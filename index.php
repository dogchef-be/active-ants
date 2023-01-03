<?php

namespace ActiveAnts;

require_once(dirname(__FILE__) . '/vendor/autoload.php');


//Include our configs
require_once(dirname(__FILE__) . '/../config.php');


//Make sure this directory is writable
$cacheDirectory = dirname(__FILE__) . '/../cache/';


App::start($url, $user, $password, $cacheDirectory);


$product = Product::model()
        ->setName('testProduct')
        ->setSku('testSku');

if (!$product->save()) {
        echo $product->getMessage();
}

$item = OrderItem::model()
        ->setSku('testSku', false)
        ->setGrossPrice(1.21)
        ->setName('testProduct')
        ->setTaxRate(21);

$address = Address::model()
        ->setName('Afosto SaaS BV')
        ->setAddress('Protonstraat', 9, 'a')
        ->setCity('Groningen')
        ->setCountry('NL')
        ->setPostalcode('9743AL');

$order = Order::model()
        ->setEmail('support@afosto.com')
        ->setOrderId('#' . 1000)
        ->setPhoneNumber('test')
        ->addOrderItem($item)
        ->setBillingAddress($address)
        ->setShippingAddress();

//$order->setPickupPoint('NL-111101', '1111AA', 'Straatnaam 10a' , 'Groningen');

if (!$order->save()) {
        echo $order->getMessage();
}

$purchase = PurchaseOrder::model()
        ->addItem('testSku', 1)
        ->addReference('testPurchaseOrder');

if (!$purchase->save()) {
        echo $purchase->getMessage();
}

foreach (Stock::model()->findAll() as $stock) {
        echo $stock->sku . ': ' . $stock->stock . "\n";
}
