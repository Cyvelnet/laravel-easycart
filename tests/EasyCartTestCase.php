<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/18/2017
 * Time: 1:57 AM
 */
class EasyCartTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Set the package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [\Cyvelnet\EasyCart\EasyCartServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('session.driver', 'array');
    }

    /**
     * get a cart instance
     *
     * @return \Cyvelnet\EasyCart\CartInstanceBridge
     */
    protected function getCartInstance()
    {
        $bridge = $this->app->make('easycart');
        $bridge->instance();

        return $bridge;

    }

}
