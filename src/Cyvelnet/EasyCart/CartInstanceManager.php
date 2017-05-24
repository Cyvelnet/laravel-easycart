<?php

namespace Cyvelnet\EasyCart;

use Cyvelnet\EasyCart\Collections\CartConditionCollection;
use DateTime;

/**
 * Class CartInstanceManager.
 */
class CartInstanceManager
{
    const DEFAULT_INSTANCE_NAME = 'default';

    const CART_PREFIX = 'easycart_';

    /**
     * @var
     */
    private $session;

    /**
     * @var
     */
    private $instance;

    /**
     * @var
     */
    private $conditions;

    /**
     * CartInstanceManager constructor.
     */
    public function __construct($session)
    {
        $this->session = $session;
        $this->conditions = new CartConditionCollection();
    }

    /**
     * @param $instance
     *
     * @return \Cyvelnet\EasyCart\Cart
     */
    public function get($instance)
    {
        $name = $this->getInstanceName($instance);

        $this->instance = $name;

        return $this->session->get($name, new Cart($name));
    }

    /**
     * create a new cart instance.
     *
     * @param $instance
     * @param null|DateTime $expiration
     *
     * @return \Cyvelnet\EasyCart\Cart
     */
    public function create($instance, $expiration = null)
    {
        $name = $this->getInstanceName($instance);

        $this->instance = $name;

        $cart = new Cart($name, $expiration);

        // apply global conditions
        $this->conditions->each(function ($condition) use ($cart) {

            $cart->condition($condition);

        });

        return $cart;

    }

    /**
     * verify if an instance already been initialized.
     *
     * @param $instance
     *
     * @return bool
     */
    public function has($instance)
    {
        $name = $this->getInstanceName($instance);

        return $this->session->has($name);
    }

    /**
     * get current instance name.
     *
     * @return string
     */
    public function getCurrentInstanceName()
    {
        return str_replace(self::CART_PREFIX, '', $this->instance);
    }

    public function addGlobalCondition($name, $value, $type = 'discount')
    {
        $condition = new CartCondition($name, $value, $type);

        if ($this->conditions->doesntHaveCondition($condition)) {

            $this->conditions->push($condition);

        }

    }

    /**
     * @param $instance
     *
     * @return string
     */
    protected function getInstanceName($instance)
    {
        $instance = $instance ?: self::DEFAULT_INSTANCE_NAME;
        $prefix = self::CART_PREFIX;

        return "{$prefix}{$instance}";
    }
}
