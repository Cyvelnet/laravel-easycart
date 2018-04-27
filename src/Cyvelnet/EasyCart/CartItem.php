<?php

namespace Cyvelnet\EasyCart;

use Cyvelnet\EasyCart\Collections\CartConditionCollection;
use Cyvelnet\EasyCart\Collections\CartItemAttributeCollection;
use Cyvelnet\EasyCart\Contracts\ConditionableContract;
use Illuminate\Support\Arr;

/**
 * Class CartItem.
 */
class CartItem extends ConditionableContract
{
    /**
     * @var
     */
    public $rowId;
    /**
     * @var
     */
    public $id;
    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $price;
    /**
     * @var
     */
    public $qty;
    /**
     * @var \Cyvelnet\EasyCart\Collections\CartItemAttributeCollection
     */
    public $attributes;
    /**
     * @var float|int
     */
    public $weight;
    /**
     * @var
     */
    private $conditions;

    /**
     * CartItem constructor.
     *
     * @param $id
     * @param $name
     * @param $price
     * @param $qty
     * @param array $attributes
     * @param float $weight
     */
    public function __construct($id, $name, $price, $qty, $attributes = [], $weight = 0.0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->qty = $qty;
        $this->attributes = new CartItemAttributeCollection($attributes);
        $this->weight = $weight;
        $this->rowId = $this->generateRowId();
        $this->conditions = new CartConditionCollection();
    }

    /**
     * get cart item subtotal.
     *
     * @return mixed
     */
    public function subtotal()
    {
        return $this->getPrice() * $this->getQty();
    }

    /**
     * get cart item total.
     *
     * @return mixed
     */
    public function total()
    {
        return $this->calculateTotal();
    }

    /**
     * get cart item total without a condition by type.
     *
     * @param $type
     *
     * @return mixed
     */
    public function totalWithoutConditionType($type)
    {
        $sum = $this->subtotal();

        $this->getConditionsWithoutType($type)->each(function (CartCondition $condition) use (&$sum) {
            $sum += $this->calculateValue($condition->getValue(), $sum, $condition->maxValue());
        });

        // calculate tax after all conditions
        return $this->calculateTaxes($sum);
    }

    /**
     * get total weight of cart item.
     *
     * @return float|int
     */
    public function getTotalWeight()
    {
        return $this->getWeight() * $this->getQty();
    }

    /**
     * @return string
     */
    public function getRowId()
    {
        return $this->rowId;
    }

    /**
     * get cart item product id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get cart item product name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get cart item product price.
     *
     * @return int|float|float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * get cart item product purchase qty.
     *
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * get cart item product attributes.
     *
     * @return \Cyvelnet\EasyCart\Collections\CartItemAttributeCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * get cart item weight.
     *
     * @return float|int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * add qty to the current item.
     *
     * @param int $qty
     */
    public function addQty($qty = 1)
    {
        if (is_int($qty) && $qty >= 1) {
            $this->qty += $qty;
        }
    }

    /**
     * merge & overrides cart item values.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function mergeFromArray($attributes = [])
    {
        if (false === app('events')->fire('cart_item.updating', [$this])) {
            return false;
        }

        $allowedAttributes = Arr::only($attributes, ['name', 'price', 'qty', 'attributes']);

        $this->name = Arr::get($allowedAttributes, 'name', $this->name);
        $this->price = Arr::get($allowedAttributes, 'price', $this->price);
        $this->qty = Arr::get($allowedAttributes, 'qty', $this->qty);
        $this->attributes = new CartItemAttributeCollection(Arr::get($allowedAttributes, 'attributes',
            $this->attributes->toArray()));

        app('events')->fire('cart_item.updated', [$this]);

        return true;
    }

    /**
     * add condition.
     *
     * @param array|\Cyvelnet\EasyCart\CartCondition $condition
     *
     * @return bool
     */
    public function condition($condition)
    {
        if (is_array($condition)) {
            foreach ($condition as $item) {
                $this->condition($item);
            }
        }

        // ensure the condition should be apply to this products
        if ($condition instanceof CartCondition && $this->conditions->doesntHaveCondition($condition)
            && (count($condition->getProducts()) === 0 || in_array($this->getId(), $condition->getProducts()))
            && ($condition->getTarget() === 'products')
        ) {
            if (false === app('events')->fire('cart_item.condition.adding', [$this])) {
                return false;
            }

            $this->conditions->push($condition);

            app('events')->fire('cart_item.condition.added', [$condition, $this]);

            return true;
        }

        return false;
    }

    /**
     * get applied conditions.
     *
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * remove a condition by its type.
     *
     * @param $type
     */
    public function removeConditionByType($type)
    {
        $toRemoves = $this->getConditions()->filter(function ($item) use ($type) {
            return $item->getType() === $type;
        });

        $toRemoves->each(function ($item, $key) use ($type) {
            $this->conditions->forget($key);
        });
    }

    /**
     * remove a condition by its name.
     *
     * @param $name
     */
    public function removeConditionByName($name)
    {
        $toRemoves = $this->getConditions()->filter(function ($item) use ($name) {
            return $item->getName() === $name;
        });

        $toRemoves->each(function ($item, $key) {
            $this->conditions->forget($key);
        });
    }

    /**
     * remove all conditions.
     */
    public function removeAllConditions()
    {
        $this->getConditions()->each(function ($item, $key) {
            $this->conditions->forget($key);
        });
    }

    /**
     * @return string
     */
    private function generateRowId()
    {
        $attributes = $this->getAttributes()->toArray();
        // key sort the item attributes before generate a hash
        ksort($attributes);

        return md5($this->getId().serialize($attributes));
    }

    /**
     * get the conditions that should be calculated.
     *
     * @return mixed
     */
    protected function getCalculateableCondition()
    {
        return $this->getConditions()->filter(function (CartCondition $condition) {
            return $condition->getTarget() === 'products';
        });
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }
}
