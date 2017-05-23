Laravel EasyCart
================
[![StyleCI](https://styleci.io/repos/92111884/shield?branch=master)](https://styleci.io/repos/92111884)
[![Build Status](https://travis-ci.org/Cyvelnet/laravel-easycart.svg?branch=master)](https://travis-ci.org/Cyvelnet/laravel-easycart)
[![Total Downloads](https://poser.pugx.org/cyvelnet/laravel-easycart/downloads)](https://packagist.org/packages/cyvelnet/laravel-easycart)
[![Latest Stable Version](https://poser.pugx.org/cyvelnet/laravel-easycart/v/stable)](https://packagist.org/packages/cyvelnet/laravel-easycart)
[![Latest Unstable Version](https://poser.pugx.org/cyvelnet/laravel-easycart/v/unstable)](https://packagist.org/packages/cyvelnet/laravel-easycart)
[![License](https://poser.pugx.org/cyvelnet/laravel-easycart/license)](https://packagist.org/packages/cyvelnet/laravel-easycart)

This project is still under development, it is far from ready for production.

## Installation
Require this package with composer using the following command:
````bash 
composer require cyvelnet/laravel-easycart
````
After updating composer, add the ServiceProvider to the providers array in config/app.php 
````php
Cyvelnet\EasyCart\EasyCartServiceProvider::class,
````

and register Facade
And optionally add a new line to the `aliases` array:

	'EasyCart' => Cyvelnet\EasyCart\Facades\EasyCart::class,

* [Usage](#usage)
* [Filtering](#filtering)
* [Condition](#condition)


## Usage

### EasyCart::add()
Add new product or update quantity on existing cart item
```php 

// Add product to cart
EasyCart::add($id, $name, $price, $qty);

// Add product to cart with attributes & weight
EasyCart::add($id, $name, $price, $qty, $attributes = [], $weight);

// Add multiple products
EasyCart::add([
    [
        'id' => '1'
        'name' => 'Product 1'
        'price' => '199.99'
        'qty' => '1',
        'attributes' => ['color' => 'red'],
        'weight' => 0.5
    ],
    [
        'id' => '2'
        'name' => 'Product 2'
        'price' => '299.99'
        'qty' => '1'
    ]
]);

```

### EasyCart::update()
Update cart item
* `An unique rowId is assigned to each cart item, use `getRowId()` on cart item to retrieves rowId `*

```php 

// update qty
EasyCart::update($rowId, $qty);

// update other 
EasyCart::update($rowId, [

    'attributes' => ['color' => 'green'],
    'qty' => 2,
    'price' => 399.99
    
]);

```

### EasyCart::remove()
Remove an item from cart

```php

EasyCart::remove($rowId);

```
### EasyCart::destroy()
Wipe cart completely

```php

EasyCart::destroy();

```

### EasyCart::qty()
Get the total number of quantity in cart.

```php 

EasyCart::qty();

```
### EasyCart::subtotal()
Get the cart subtotal before a condition value is being added, use `EasyCart::total()` to retrieves the final price

```php 

EasyCart::subtotal();

```
### EasyCart::total()
Get the cart total with condition values calculated

```php 

EasyCart::total();

```

### EasyCart::items()
Get the cart items `EasyCart::content() is an aliase to EasyCart::items()`, `Cyvelnet\EasyCart\Collections\CartItemCollection` instance is return

```php 

EasyCart::items()

```

### EasyCart::weight()
Get the cart total weight
```php 

EasyCart::weight()

```


## Filtering

### EasyCart::find()
Find a cart item by product id, a `Cyvelnet\EasyCart\CartItem` instance is return

```php 

EasyCart::find($id);

```
### EasyCart::findByIds()
Find a cart item by an array of ids, a `Cyvelnet\EasyCart\Collections\CartItemCollection` instance is return

```php 

EasyCart::findByIds($ids = []);

```

## Condition
EasyCart support condition, which is essential to ECommerces application, either provides discount or add additional prices are supported.
 
### EasyCart::condition()
Adding a condition is simple, just instantiate a Cyvelnet\EasyCart\CartCondition object and you are ready to go.

```php 
// Add a 50% discount to cart

$50PercentDiscount = new CartCondition($name = '$50 Off', $value = '-50') // you have to use a - (minus sign) to indicate a discount is expected

EasyCart::condition($50PercentDiscount);

```

Sometimes you want to only give an discount to only to a selected range of products instead of apply to the whole cart, it is easy

```php 

$50PercentDiscount = new CartCondition($name = '$50 Off', $value = '-50');
$50PercentDiscount->onProduct([1,2,3,4]);

EasyCart::condition($50PercentDiscount);

```

Life is not always easy, what if you need to give an discount of 20% but with a maximum up to $50 ?

```php 

$50PercentDiscount = new CartCondition($name = '20% Off', $value = '-20');
$50PercentDiscount->maxAt(50);

EasyCart::condition($50PercentDiscount);

```

### EasyCart::removeConditionByType()
Remove condition by type

```php 

EasyCart::removeConditionByType($type);

```

### EasyCart::removeConditionByName()

Remove condition by name

```php 

EasyCart::removeConditionByName($name);

```

## Instances
EasyCart support multiple instances, no extra configuration is needed, just point it to instance and it works the same as normal

```php 

EasyCart::instance('wishlist')->add($id, $name, $price, $qty);
EasyCart::instance('wishlist')->destroy();

```

### Instances Expiration
Sometimes a cart expiration is needed, maybe for reservation, or other usage, this is handful in EasyCart

```php 

// create a new instances with 15 minutes expiration
EasyCart::instance('reservation', \Carbon::now()->addMinutes(15));

```

To verify whether a cart is expired, use `EasyCart::isExpired()`

``` php 

// check if a cart is expired
EasyCart::instance('reservation')->isExpired();

```

Since you may expire a cart, you might want to make a countdown too

```php 

EasyCart::instance('reservation')->expirationTimestamp();

```
