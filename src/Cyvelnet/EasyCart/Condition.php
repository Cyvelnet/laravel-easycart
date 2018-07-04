<?php

namespace Cyvelnet\EasyCart;

/**
 * Class Condition.
 */
abstract class Condition
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $value;
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var array
     */
    protected $products;

    /**
     * @var int|float
     */
    protected $maxValue;

    /**
     * @var bool|int|float
     */
    protected $applyMinimum = false;
/**
     * @var bool
     */
    protected $applyMinimumForEach = false;

    /**
     * set the condition target.
     *
     * @return string
     */
    protected function on()
    {
        return 'subtotal';
    }

    /**
     * set condition only apply to a range of product ids.
     *
     * @return array
     */
    protected function only()
    {
        return [];
    }

    /**
     * set the maximum value when using percentage condition value.
     *
     * @return int|float
     */
    protected function max()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return float|int
     */
    public function maxValue()
    {
        return $this->maxValue;
    }
    
    /**
     * @return bool|float|int
     */
    public function getApplyMinimum()
    {
        return $this->applyMinimum;
    }
    
    /**
     * @return bool|float|int
     */
    public function getApplyMinimumForEach()
    {
        return $this->applyMinimumForEach;
    }


    /**
     * determine if a subtotal has passed the given apply minumn & maximum rule
     *
     * @param $subtotal
     *
     * @return bool
     */
    public function isFulfillMinimum($subtotal)
    {
        if (!$this->applyMinimum) {
            return true;
        }


        if(count($this->products) > 0 && !$this->applyMinimumForEach){

            return false;
        }


        return $this->applyMinimum <= $subtotal;

    }

}
