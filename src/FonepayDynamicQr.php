<?php

namespace ShootKiran\DynamicQrGeneratorFonepay;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShootKiran\DynamicQrGeneratorFonepay\Enums\FonepayStatus;
use Illuminate\Support\Facades\Http;
use ShootKiran\DynamicQrGeneratorFonepay\Facades\FonepayDynamicQr as FacadesFonepayDynamicQr;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FonepayDynamicQr extends Model
{
    use HasFactory;

    protected $fillable = [
        'prn',
        'amount',
        'remarks1',
        'remarks2',

        'username',
        'password',
        'secretKey',
        'merchantCode',

        'status',
        'verified_at',
        'fonepay_qrMessage',
        'fonepay_status',
        'fonepay_requested_date',
        'fonepay_merchantWebSocketUrl',
        'fonepay_thirdpartyQrWebSocketUrl',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'status' => FonepayStatus::class,
    ];
    protected static function getConfig()
    {
        return config('fonepay');
    }
    public function QrCodeImage($size = '400')
    {
        return QrCode::size($size)->generate($this->fonepay_qrMessage);
    }
    public static function generate(
        $amount,
        $remarks1,
        $remarks2,
        $prn,
        $merchantCode = null,
        $username = null,
        $password = null,
        $secretKey = null,
        $taxAmount = null,
        $taxRefund = null,
    ) {
        $config = self::getConfig();
        $url = $config['fonepay_dynamicqr_url'] . '/thirdPartyDynamicQrDownload';
        $merchantCode = $merchantCode ?? $config['fonepay_dynamicqr_merchantcode'] ?? null;
        $username = $username ?? $config['fonepay_dynamicqr_username'] ?? null;
        $password = $password ?? $config['fonepay_dynamicqr_password'] ?? null;
        $secretKey = $secretKey ?? $config['fonepay_dynamicqr_secret'] ?? null;

        if (!$merchantCode || !$username || !$password || !$secretKey) {
            throw new \Exception("Fonepay credentials are missing.");
        }


        $data_to_hash = "$amount,$prn,$merchantCode,$remarks1,$remarks2";
        $dataValidation = hash_hmac('sha512', $data_to_hash, $secretKey);

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
        $record = FonepayDynamicQr::firstOrCreate([
            'prn' => $prn
        ], [
            'amount' => $amount,
            'remarks1' => $remarks1,
            'remarks2' => $remarks2,
            'username' => $username,
            'password' => $password,
            'merchantCode' => $merchantCode,
            'secretKey' => $secretKey,
            'status' => FonepayStatus::PENDING,
        ]);
        if ($record->wasRecentlyCreated || $record->fonepay_qrMessage == null) {
            $response = Http::post($url, $data)->object();
            if (!$response->success) {
                $record->update([
                    'status' => FonepayStatus::FAILED,
                    'fonepay_status' => $response->status,
                    'fonepay_message' => $response->message,
                ]);
                throw new \Exception("Status: {$response->status} \n Message: {$response->message}", $response->statusCode);
            }
            $record->update([
                'status' => FonepayStatus::PENDING,
                'fonepay_qrMessage' => $response->qrMessage,
                'fonepay_requested_date' => $response->requested_date,
                'fonepay_merchantWebSocketUrl' => $response->merchantWebSocketUrl,
                'fonepay_thirdpartyQrWebSocketUrl' => $response->thirdpartyQrWebSocketUrl,
            ]);
        }

        return $record;
    }

    public function verify()
    {
        $config = self::getConfig();
        $url = $config['fonepay_dynamicqr_url'] . '/thirdPartyDynamicQrGetStatus';

        $data_to_hash = "$this->prn,$this->merchantCode";
        $dataValidation = hash_hmac('sha512', $data_to_hash, $this->secretKey);

        $data = [
            "prn" => $this->prn,
            "merchantCode" => $this->merchantCode,
            "dataValidation" => $dataValidation,
            "username" => $this->username,
            "password" => $this->password
        ];
        $response = Http::post($url, $data)->object();
        if ($response->paymentStatus == "success") {
            $this->update([
                'status' => FonepayStatus::SUCCESS,
                'fonepay_status' => $response->paymentStatus,
                'fonepay_message' => "TraceId:" . $response->fonepayTraceId,
                'verified_at' => now(),
            ]);
        } else {
            $this->update([
                'status' => FonepayStatus::PENDING,
                'fonepay_status' => $response->paymentStatus,
                'verified_at' => now(),
            ]);
        }
    }
}
