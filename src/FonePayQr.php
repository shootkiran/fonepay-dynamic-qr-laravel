<?php

namespace ShootKiran\DynamicQrGeneratorFonepay;

use Illuminate\Support\Facades\Http;


class FonePayQR
{
    protected $config;

    public function __construct()
    {
        $this->config = config('fonepay');
    }

    public function generate(
        $amount,
        $remarks1,
        $remarks2,
        $prn,
        $merchantCode = null,
        $username = null,
        $password = null,
        $secret_key = null,
        $taxAmount = null,
        $taxRefund = null
    ) {
        $url = $this->config['fonepay_dynamicqr_url'] . '/thirdPartyDynamicQrDownload';
        if (is_null($merchantCode)) {
            $merchantCode = $this->config('fonepay_dynamicqr_merchantcode');
            if ($merchantCode == "") {
                throw new \Exception("Fonepay Merchant Code is Missing");
            }
        }
        if (is_null($username)) {
            $username = $this->config('fonepay_dynamicqr_username');
            if ($username == "") {
                throw new \Exception("Fonepay Username is Missing");
            }
        }
        if (is_null($password)) {
            $password = $this->config('fonepay_dynamicqr_password');
            if ($password == "") {
                throw new \Exception("Fonepay Password is Missing");
            }
        }
        if (is_null($secret_key)) {
            $secret_key = $this->config('fonepay_dynamicqr_secret');
            if ($secret_key == "") {
                throw new \Exception("Fonepay Secret is Missing");
            }
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
        return isset($response->success) ? $response : false;
    }

    public function verify($prn, $merchantCode, $username, $password, $secret_key)
    {
        $url = $this->config['api_url'] . '/thirdPartyDynamicQrGetStatus';
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
