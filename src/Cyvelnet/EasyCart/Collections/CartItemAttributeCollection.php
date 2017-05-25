<?php

namespace Cyvelnet\EasyCart\Collections;

use Illuminate\Support\Collection;

/**
 * Class CartAttributeCollection.
 */
class CartItemAttributeCollection extends Collection
{
    /**
     * access attribute in the collection with a given key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
}
