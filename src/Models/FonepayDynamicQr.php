<?php

namespace ShootKiran\FonepayDynamicQrLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FonepayDynamicQr extends Model
{
    use HasFactory;

    protected $fillable = [
        'prn',
        'amount',
        'remarks1',
        'remarks2',
        'merchant_code',
        'qr_code_url',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}
