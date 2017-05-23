<?php

namespace Cyvelnet\EasyCart;

use Illuminate\Support\ServiceProvider;

/**
 * Class EasyCartServiceProvider
 */
class EasyCartServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/easycart.php', 'easycart');

        $this->app->singleton('easycart', function ($app) {

            $manager = new CartInstanceManager($app['session'], $app['events']);

            $cart = new CartInstanceBridge($manager);

            $cart->instance();

            return $cart;

        });


    }

}
