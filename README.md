# Active Ants API client


This package provides a wrapper for the ActiveAnts ShopApi. This package was developed
by Afosto to make a reliable connection between Afosto (Retail Software) and Active Ants
and provides all the basic functionality.


## Installation

To install, use composer:

```
composer require afosto/active-ants
```

## Usage

First get an account at ActiveAnts and obtain a username and password for the 
ShopApi.

Start the app with the following code. The application will obtain an authorization-token, 
retreive settings and cache these in the cache folder.

```php
App::start($endpoint, $username, $password, $cacheDirectory);
```

Below you'll find a subset of the available methods.


### Create a product

```php
$product = Product::model()
        ->setName('testProduct')
        ->setSku('testSku');

if ($product->save()) {
    echo "Product was saved";
}
```


### Create an order

```php
$item = OrderItem::model()
        ->setSku('testSku')
        ->setGrossPrice(1.21)
        ->setName('Test Product')
        ->setTaxRate(21);

$address = Address::model()
        ->setName('Afosto SaaS BV')
        ->setAddress('Protonstraat', 9, 'a')
        ->setCity('Groningen')
        ->setCountry('NL')
        ->setPostalcode('9743AL');

$order = Order::model()
        ->setEmail('support@afosto.com')
        ->setOrderId('#1')
        ->setPhoneNumber('0507119519')
        ->addOrderItem($item)
        ->setBillingAddress($address)
        ->setShippingAddress($address);

if ($order->save()) {
    echo "Order was saved";
}
```


### Get stock for all products

```php
foreach (Stock::model()->findAll() as $stock) {
    echo $stock->sku . ': ' . $stock->stock . "\n";
}
```