<?php

namespace Cyvelnet\EasyCart;

/**
 * Class CartCondition.
 */
class CartCondition extends Condition
{
    /**
     * CartCondition constructor.
     *
     * @param $name
     * @param float|string $value
     * @param string       $type
     */
    public function __construct($name, $value, $type = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->onCart();
        $this->products = [];
    }

    /**
     * apply condition on cart subtotal.
     *
     * @return $this
     */
    public function onCart()
    {
        $this->target = 'subtotal';

        return $this;
    }

    /**
     * apply condition on cart item.
     *
     * @param array $only Specify product ids a condition should apply
     *
     * @return $this
     */
    public function onProduct($only = [])
    {
        $this->target = 'products';
        $this->products = $only;

        return $this;
    }

    /**
     * set the maximum discount value limit.
     *
     * @param int|float $max
     *
     * @return $this
     */
    public function maxAt($max)
    {
        $this->maxValue = $max;

        return $this;
    }

    /**
     * 
     * @param int|float $value
     * @param bool $each Determine if minimum should apply per cart item, when apply condition to a group of products
     * 
     * @return $this
     * 
     */

    public function applyWithMinimum($value, $each = false)
    {
        $this->applyMinimum = $value;
        $this->applyMinimumForEach = $each;

        return $this;
    }
}
