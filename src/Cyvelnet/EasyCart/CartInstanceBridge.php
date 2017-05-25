<?php

namespace Cyvelnet\EasyCart;

use Cyvelnet\EasyCart\Contracts\ConditionableContract;
use Cyvelnet\EasyCart\Contracts\ManipulatableInterface;

/**
 * Class Cart.
 */
class CartInstanceBridge extends ConditionableContract implements ManipulatableInterface
{
    /**
     * @var string
     */
    private $instance;

    /**
     * @var \Cyvelnet\EasyCart\CartInstanceManager
     */
    private $manager;

    /**
     * Cart constructor.
     *
     * @param \Cyvelnet\EasyCart\CartInstanceManager $manager
     *
     * @internal param $session
     * @internal param \Illuminate\Events\Dispatcher $event
     */
    public function __construct(CartInstanceManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return string
     */
    public function getCurrentInstanceName()
    {
        return $this->manager->getCurrentInstanceName();
    }

    /**
     * @return mixed
     */
    public function getCurrentInstance()
    {
        return $this->instance;
    }

    /**
     * @param $instance
     * @param null|\DateTime $expiredAt
     *
     * @return \Cyvelnet\EasyCart\Cart
     */
    public function instance($instance = null, $expiredAt = null)
    {
        $this->instance = $this->manager->has($instance) ? $this->manager->get($instance) : $this->manager->create($instance,
            $expiredAt);

        return $this->instance;
    }

    /**
     * verify if a cart is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->instance->isExpired();
    }

    /**
     * get the expiration timestamps.
     *
     * @return int|null
     */
    public function expirationTimestamp()
    {
        return $this->instance->expirationTimestamp();
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
        return $this->instance->get($rowId);
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
     */
    public function add($id, $name = null, $price = null, $qty = null, $attributes = [], $weight = 0.0)
    {
        $this->instance->add($id, $name, $price, $qty, $attributes, $weight);
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
        return $this->instance->remove($rowId);
    }

    /**
     * update cart item by rowId.
     *
     * @param $rowId
     * @param array|int $qty
     *
     * @return bool
     */
    public function update($rowId, $qty)
    {
        return $this->instance->update($rowId, $qty);
    }

    /**
     * destroy a cart.
     */
    public function destroy()
    {
        return $this->instance->destroy();
    }

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function content()
    {
        return $this->instance->content();
    }

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function items()
    {
        return $this->instance->items();
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
        return $this->instance->find($id);
    }

    /**
     * filter out cart items by matching cart item id against an array of ids.
     *
     * @param array $ids
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function findByIds($ids = [])
    {
        return $this->instance->findByIds($ids = []);
    }

    /**
     * get the total quantity of all cart items.
     *
     * @return int
     */
    public function qty()
    {
        return $this->instance->qty();
    }

    /**
     * get the total weight per cart.
     *
     * @return float|int
     */
    public function weight()
    {
        return $this->instance->weight();
    }

    /**
     * get cart total without charges.
     *
     * @return float|int
     */
    public function subtotal()
    {
        return $this->instance->subtotal();
    }

    /**
     * get cart total with charges.
     *
     * @return float|int
     */
    public function total()
    {
        return $this->instance->total();
    }

    /**
     * add cart condition.
     *
     * @param array|CartCondition $condition
     */
    public function condition($condition)
    {
        $this->instance->condition($condition);
    }

    /**
     * get applied conditions.
     *
     * @return mixed
     */
    public function getConditions()
    {
        return $this->instance->getConditions();
    }

    /**
     * remove a condition by its type.
     *
     * @param $type
     */
    public function removeConditionByType($type)
    {
        $this->instance->removeConditionByType($type);
    }

    /**
     * remove a condition by its name.
     *
     * @param $name
     */
    public function removeConditionByName($name)
    {
        $this->instance->removeConditionByName($name);
    }
}
