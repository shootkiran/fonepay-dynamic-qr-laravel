<?php

namespace ShootKiran\DynamicQrGeneratorFonepay\Facades;

use Illuminate\Support\Facades\Facade;
use ShootKiran\DynamicQrGeneratorFonepay\FonepayDynamicQr as ModelsFonepayDynamicQr;

class FonepayDynamicQr extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fonepaydynamicqr';
    }
}
