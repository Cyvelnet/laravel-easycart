<?php

namespace Cyvelnet\EasyCart\Collections;

use Cyvelnet\EasyCart\CartCondition;
use Illuminate\Support\Collection;

/**
 * Class CartConditionCollection.
 */
class CartConditionCollection extends Collection
{
    /**
     * check if a given condition has been added.
     *
     * @param \Cyvelnet\EasyCart\CartCondition $condition
     *
     * @return bool
     */
    public function doesntHaveCondition(CartCondition $condition)
    {
        return $this->filter(function ($item) use ($condition) {
            return $item == $condition;
        })->count() === 0;
    }

    /**
     * check if a given condition has been added.
     *
     * @param \Cyvelnet\EasyCart\CartCondition $condition
     *
     * @return bool
     */
    public function hasCondition(CartCondition $condition)
    {
        return $this->filter(function ($item) use ($condition) {
            return $item == $condition;
        })->count() > 0;
    }
}
