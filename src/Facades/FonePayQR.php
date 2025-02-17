<?php
namespace ShootKiran\DynamicQrGeneratorFonepay\Facades;

use Illuminate\Support\Facades\Facade;

class FonePayQR extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fonepayqr';
    }
}
