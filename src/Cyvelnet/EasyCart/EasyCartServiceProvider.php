<?php

namespace Cyvelnet\EasyCart;

use Illuminate\Support\ServiceProvider;

/**
 * Class EasyCartServiceProvider.
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
     * Boot the service provider.
     */
    public function boot()
    {
<<<<<<< HEAD

        $this->publishes([
            __DIR__ . '/../config/easycart.php' => 'config/easycart.php',
        ], 'easycart');

        $this->loadViewsFrom(__DIR__ . '/../views', 'easycart');
=======
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/config/easycart.php' => config_path('easycart.php'),
            ], 'easycart');
        }
>>>>>>> 41cf0998bddc1531eca10cc8e7b72273e5592ed3
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
<<<<<<< HEAD

        $source_config = __DIR__.'/../config/easycart.php';
        $this->mergeConfigFrom($source_config, 'easycart');
=======
        $this->mergeConfigFrom(__DIR__.'/config/easycart.php', 'easycart');
>>>>>>> 41cf0998bddc1531eca10cc8e7b72273e5592ed3

        $this->app->singleton('easycart', function ($app) {
            $manager = new CartInstanceManager($app['session'], $app['events']);

            $cart = new CartInstanceBridge($manager);

            $cart->instance();

            $taxes = $app['config']->get('easycart.taxes');

            foreach ($taxes as $name => $value) {

                // add global condition
                $manager->addGlobalCondition($name, $value, 'tax');
            }

            return $cart;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
