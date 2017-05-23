<?php

namespace Cyvelnet\EasyCart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class EasyCart.
 */
class EasyCart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'easycart';
    }
}
