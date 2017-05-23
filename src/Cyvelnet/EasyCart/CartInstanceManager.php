<?php

namespace Cyvelnet\EasyCart;

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
     * CartInstanceManager constructor.
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * @param $instance

     *
     *@return \Cyvelnet\EasyCart\Cart
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
     *@param $instance
     * @param null|DateTime $expiration
     *
     *@return \Cyvelnet\EasyCart\Cart
     */
    public function create($instance, $expiration = null)
    {
        $name = $this->getInstanceName($instance);

        $this->instance = $name;

        return new Cart($name, $expiration);
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
