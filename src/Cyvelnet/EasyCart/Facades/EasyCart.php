<?php

namespace Cyvelnet\EasyCart\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class EasyCart
 *
 * @package Cyvelnet\EasyCart\Facades
 */
class EasyCart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'easycart';
    }

}
