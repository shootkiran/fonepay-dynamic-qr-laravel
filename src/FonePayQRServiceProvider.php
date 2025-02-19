<?php

namespace ShootKiran\DynamicQrGeneratorFonepay;

use Illuminate\Support\ServiceProvider;
use ShootKiran\DynamicQrGeneratorFonepay\Facades\FonepayDynamicQr;

class FonePayQRServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fonepay.php', 'fonepay');

        $this->app->singleton(FonepayDynamicQr::class, function ($app) {
            return new FonepayDynamicQr();
        });

        $this->app->alias(FonepayDynamicQr::class, 'fonepaydynamicqr');
    }

    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/config/fonepay.php' => config_path('fonepay.php'),
        ], 'config');
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
