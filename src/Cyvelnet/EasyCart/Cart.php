<?php

namespace Cyvelnet\EasyCart;

/*
 * Class CartInstance
 *
 * @package Cyvelnet\EasyCart
 */
use Cyvelnet\EasyCart\Collections\CartConditionCollection;
use Cyvelnet\EasyCart\Collections\CartItemCollection;
use Cyvelnet\EasyCart\Contracts\ConditionableContract;
use Cyvelnet\EasyCart\Contracts\ManipulatableInterface;
use DateTime;

/**
 * Class CartInstance.
 */
class Cart extends ConditionableContract implements ManipulatableInterface
{
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $expiration;
    /**
     * @var \Cyvelnet\EasyCart\Collections\CartItemCollection
     */
    protected $items;

    /**
     * @var \Cyvelnet\EasyCart\Collections\CartConditionCollection
     */
    protected $conditions;

    /**
     * CartInstance constructor.
     *
     * @param $name
     * @param null $expiration
     */
    public function __construct($name, $expiration = null)
    {
        $this->name = $name;
        $this->expiration = $expiration;
        $this->items = new CartItemCollection();
        $this->conditions = new CartConditionCollection();
    }

    /**
     * get cart items stored in an cart instance.
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function getCartItemCollection()
    {
        return $this->items;
    }

    /**
     * verify if a cart is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->expiration) {
            return false;
        }

        return $this->expiration < new DateTime();
    }

    /**
     * get the expiration timestamp.
     *
     * @return int|null
     */
    public function expirationTimestamp()
    {
        if ($this->expiration instanceof \DateTime) {
            return $this->expiration->getTimestamp();
        }
    }

    /**
     * get a cart item by rowId.
     *
     * @param $rowId
     *
     * @return mixed
     */
    public function get($rowId)
    {
        return $this->getCartItemCollection()->get($rowId);
    }

    /**
     * add product to cart.
     *
     * @param $id
     * @param null  $name
     * @param null  $price
     * @param null  $qty
     * @param array $attributes
     * @param float $weight
     *
     * @return \Cyvelnet\EasyCart\CartItem|\Cyvelnet\EasyCart\Collections\CartItemCollection
     */
    public function add($id, $name = null, $price = null, $qty = null, $attributes = [], $weight = 0.0)
    {
        // helps to add item one by one
        if (is_array($id)) {
            $addedCartItemCollection = new CartItemCollection();
            foreach ($id as $row) {
                $addedCartItemCollection->push($this->add(
                    $row['id'],
                    $row['name'],
                    $row['price'],
                    $row['qty'],
                    array_key_exists('attributes', $row) ? $row['attributes'] : [],
                    array_key_exists('weight', $row) ? $row['weight'] : 0.0
                ));
            }

            return $addedCartItemCollection;
        } else {
            $item = new CartItem($id, $name, $price, $qty, $attributes, $weight);

            // append quantity instead of insert a new rows
            if ($this->exists($item->getRowId())) {

                // reuse the existing item and update its qty accordingly
                $existedItem = $this->getCartItemCollection()->get($item->getRowId());
                $existedItem->addQty($item->getQty());

                $item = $existedItem;
            }

            return $this->addToCollection($item);
        }
    }

    /**
     * remove a cart item row.
     *
     * @param $rowId
     *
     * @return bool
     */
    public function remove($rowId)
    {
        if (!$this->exists($rowId)) {
            return false;
        }

        $item = $this->get($rowId);

        if (false === $this->triggerEvent('cart.deleting', $item)) {
            return false;
        }

        $this->getCartItemCollection()->forget($rowId);

        $this->triggerEvent('cart.deleted', $rowId, $item);

        return true;
    }

    /**
     * update cart item by rowId.
     *
     * @param $rowId
     * @param array|int $qty
     *
     * @return \Cyvelnet\EasyCart\CartItem
     */
    public function update($rowId, $qty)
    {
        $item = $this->get($rowId);

        $updateData = is_array($qty) ? $qty : ['qty' => $qty];

        $item->mergeFromArray($updateData);

        // remove the item when has 0 qty

        if ($item->qty <= 0) {
            $this->remove($rowId);

            return false;
        }

        return $this->addToCollection($item);
    }

    /**
     * destroy a cart.
     */
    public function destroy()
    {
        app('session')->forget($this->name);
    }

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\Collections\CartItemCollection
     */
    public function content()
    {
        return $this->getCartItemCollection();
    }

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\Collections\CartItemCollection
     */
    public function items()
    {
        return $this->getCartItemCollection();
    }

    /**
     * filter out cart item.
     *
     * @param string|int|callable $id
     *
     * @return mixed
     */
    public function find($id)
    {
        if (is_callable($id)) {
            return $this->getCartItemCollection()->filter($id);
        }

        return $this->getCartItemCollection()->first(function ($item) use ($id) {
            return $item->id == $id;
        });
    }

    /**
     * filter out cart items by matching cart item id against an array of ids.
     *
     * @param array $ids
     *
     * @return \Cyvelnet\EasyCart\Collections\CartItemCollection
     */
    public function findByIds($ids = [])
    {
        return $this->find(function ($item) use ($ids) {
            return in_array($item->getId(), $ids);
        });
    }

    /**
     * get the total quantity of all cart items.
     *
     * @return int
     */
    public function qty()
    {
        return $this->getCartItemCollection()->sum(function (CartItem $item) {
            return $item->getQty();
        });
    }

    /**
     * get the total weight per cart.
     *
     * @return float|int
     */
    public function weight()
    {
        return $this->getCartItemCollection()->sum(function ($item) {
            return $item->getTotalWeight();
        });
    }

    /**
     * get cart total without charges.
     *
     * @return float|int
     */
    public function subtotal()
    {
        return $this->getCartItemCollection()->sum(function (CartItem $item) {
            return $item->subtotal();
        });
    }

    /**
     * get cart total with charges.
     *
     * @return float|int
     */
    public function total()
    {
        return $this->calculateTotal();
    }

    /**
     * render cart into view.
     *
     * @param null $view
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render($view = null, $instance = null)
    {
        if (!$view) {
            $view = 'easycart::cart';
        }

        return view($view, ['cart' => $this->instance($instance)]);
    }

    /**
     * add cart condition.
     *
     * @param array|CartCondition $condition
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

        if (false === app('events')->dispatch('cart.condition.adding', $this)) {
            return false;
        }

        if ($condition instanceof CartCondition && $this->conditions->doesntHaveCondition($condition)) {
            $this->conditions->push($condition);

            $this->applyConditionToItems($condition);
        }

        $this->persistCart();

        return true;
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

            $this->getCartItemCollection()->each(function ($item) use ($type) {
                $item->removeConditionByType($type);
            });
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

        $toRemoves->each(function ($item, $key) use ($name) {
            $this->conditions->forget($key);

            $this->getCartItemCollection()->each(function ($item) use ($name) {
                $item->removeConditionByName($name);
            });
        });
    }

    /**
     * remove all conditions.
     */
    public function removeAllConditions()
    {
        $this->getConditions()->each(function ($item, $key) {
            $this->conditions->pop();
        });

        $this->getCartItemCollection()->each(function (CartItem $item) {
            $item->removeAllConditions();
        });
    }

    /**
     * add a cart item to collection.
     *
     * @param $item
     *
     * @return \Cyvelnet\EasyCart\CartItem
     */
    protected function addToCollection($item)
    {
        if (false === $this->triggerEvent('cart.adding', $item)) {
            return false;
        }

        $cart = $this->getCartItemCollection();

        $cart->put($item->rowId, $item);

        $this->applyItemConditionToAllItems();

        $this->persistCart();

        $this->triggerEvent('cart.added', $item->rowId);

        return $item;
    }

    /**
     * @param $rowId
     *
     * @return bool
     */
    protected function exists($rowId)
    {
        return $this->getCartItemCollection()->has($rowId);
    }

    /**
     * get the conditions that should be calculated.
     *
     * @return mixed
     */
    protected function getCalculateableCondition()
    {
        return $this->getConditions()->filter(function (CartCondition $condition) {
            if ($condition->getType() !== 'tax') {
                if (($condition->getApplyMinimum() && !$condition->getApplyMinimumForEach()
                && $condition->getApplyMinimum() <= $this->subtotal())) {
                    return true;
                }

                if ($condition->getTarget() !== 'products' && !$condition->getApplyMinimum()) {
                    return true;
                }
            }
        });
    }

    protected function calculateTotal()
    {
        $sum = $this->items()->sum(function (CartItem $item) {
            return $item->total();
        });

        $this->getCalculateableCondition()->each(function (CartCondition $condition) use (&$sum) {
            $sum += $this->calculateValue($condition->getValue(), $sum, $condition->maxValue());
        });

        // calculate tax after all conditions
        return $this->calculateTaxes($sum);
    }

    private function applyItemConditionToAllItems()
    {
        $this->conditions->filter(function ($item) {
            return count($item->getProducts()) > 0;
        })->each(function ($condition) {
            $this->applyConditionToItems($condition);
        });
    }

    /**
     * apply the exact condition to cart item.
     *
     * @param $condition
     */
    private function applyConditionToItems($condition)
    {
        $this->items->each(function ($item) use ($condition) {
            $item->condition(clone $condition);
        });
    }

    private function getSumOfItems(array $ids = [])
    {
        return $this->items()->filter(function ($item) use ($ids) {
            return in_array($item->id, $ids);
        })->sum(function ($item) {
            return $item->total();
        });
    }

    /**
     * @param $event
     * @param array ...$param
     *
     * @return array|null
     */
    private function triggerEvent($event, ...$param)
    {
        return app('events')->fire($event, array_merge($param, [$this]));
    }

    /**
     * @param $cart
     */
    private function persistCart()
    {
        app('session')->put($this->name, $this);
    }
}
