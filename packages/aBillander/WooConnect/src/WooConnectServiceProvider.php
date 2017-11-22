<?php

namespace aBillander\WooConnect;

use Illuminate\Support\ServiceProvider;

class WooConnectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // abi_r(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
