<?php

namespace ShootKiran\DynamicQrGeneratorFonepay;

use Illuminate\Support\ServiceProvider;

class FonePayQRServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fonepay.php', 'fonepay');

        $this->app->singleton(FonePayQR::class, function ($app) {
            return new FonePayQR();
        });

        $this->app->alias(FonePayQR::class, 'fonepayqr');
    }

    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/fonepay.php' => config_path('fonepay.php'),
        ], 'config');
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
