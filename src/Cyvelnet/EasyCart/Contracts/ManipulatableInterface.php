<?php

namespace Cyvelnet\EasyCart\Contracts;

use Cyvelnet\EasyCart\CartCondition;

/**
 * Interface ManipulatableInterface.
 */
interface ManipulatableInterface
{
    /**
     * verify if a cart is expired.
     *
     * @return bool
     */
    public function isExpired();

    /**
     * get the expiration timestamps.
     *
     * @return int|null
     */
    public function expirationTimestamp();

    /**
     * get a cart item by rowId.
     *
     * @param $rowId
     *
     * @return mixed
     */
    public function get($rowId);

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
    public function add($id, $name = null, $price = null, $qty = null, $attributes = [], $weight = 0.0);

    /**
     * remove a cart item row.
     *
     * @param $rowId
     *
     * @return bool
     */
    public function remove($rowId);

    /**
     * update cart item by rowId.
     *
     * @param $rowId
     * @param array|int $qty
     *
     * @return bool
     */
    public function update($rowId, $qty);

    /**
     * destroy a cart.
     */
    public function destroy();

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function content();

    /**
     * retrieves cart content.
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function items();

    /**
     * filter out cart item.
     *
     * @param string|int|callable $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * filter out cart items by matching cart item id against an array of ids.
     *
     * @param array $ids
     *
     * @return \Cyvelnet\EasyCart\CartItemCollection
     */
    public function findByIds($ids = []);

    /**
     * get the total quantity of all cart items.
     *
     * @return int
     */
    public function qty();

    /**
     * get the total weight per cart.
     *
     * @return float|int
     */
    public function weight();

    /**
     * get cart total without charges.
     *
     * @return float|int
     */
    public function subtotal();

    /**
     * get cart total with charges.
     *
     * @return float|int
     */
    public function total();

    /**
     * add cart condition.
     *
     * @param array|CartCondition $condition
     */
    public function condition($condition);
}
