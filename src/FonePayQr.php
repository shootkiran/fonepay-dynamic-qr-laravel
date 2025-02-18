<?php

namespace ShootKiran\DynamicQrGeneratorFonepay;

use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FonePayQR
{
    protected static function getConfig()
    {
        return config('fonepay');
    }

    public static function generate(
        $amount,
        $remarks1,
        $remarks2,
        $prn,
        $merchantCode = null,
        $username = null,
        $password = null,
        $secret_key = null,
        $qrSize = "400",
        $taxAmount = null,
        $taxRefund = null,
    ) {
        $config = self::getConfig();
        $url = $config['fonepay_dynamicqr_url'] . '/thirdPartyDynamicQrDownload';

        $merchantCode = $merchantCode ?? $config['fonepay_dynamicqr_merchantcode'] ?? null;
        $username = $username ?? $config['fonepay_dynamicqr_username'] ?? null;
        $password = $password ?? $config['fonepay_dynamicqr_password'] ?? null;
        $secret_key = $secret_key ?? $config['fonepay_dynamicqr_secret'] ?? null;

        if (!$merchantCode || !$username || !$password || !$secret_key) {
            throw new \Exception("Fonepay credentials are missing.");
        }

        $data_to_hash = "$amount,$prn,$merchantCode,$remarks1,$remarks2";
        $dataValidation = hash_hmac('sha512', $data_to_hash, $secret_key);

        $data = [
            "amount" => $amount,
            "remarks1" => $remarks1,
            "remarks2" => $remarks2,
            "prn" => $prn,
            "merchantCode" => $merchantCode,
            "dataValidation" => $dataValidation,
            "username" => $username,
            "password" => $password
        ];
        $response = Http::post($url, $data)->object();
        if ($response->success) {
            $qrmessage = $response->qrMessage;
            $requested_date = $response->requested_date;
            return QrCode::size($qrSize)->generate($qrmessage);
        }
        return isset($response->success) ? $response : false;
    }

    public static function verify($prn, $merchantCode, $username, $password, $secret_key)
    {
        $config = self::getConfig();
        $url = $config['fonepay_dynamicqr_url'] . '/thirdPartyDynamicQrGetStatus';

        $data_to_hash = "$prn,$merchantCode";
        $dataValidation = hash_hmac('sha512', $data_to_hash, $secret_key);

        $data = [
            "prn" => $prn,
            "merchantCode" => $merchantCode,
            "dataValidation" => $dataValidation,
            "username" => $username,
            "password" => $password
        ];

        $response = Http::post($url, $data)->object();
        return isset($response->paymentStatus) ? $response : false;
    }
}
