<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BmiGatewayService
{
    private string $terminalId;
    private string $merchantId;
    private string $key;
    private bool $verifySsl;
    private string $requestUrl;
    private string $purchaseUrl;
    private string $verifyUrl;

    public function __construct()
    {
        $this->terminalId = (string) config('services.bmi.terminal_id');
        $this->merchantId = (string) config('services.bmi.merchant_id');
        $this->key = (string) config('services.bmi.key');
        $this->verifySsl = (bool) config('services.bmi.verify_ssl', true);
        $this->requestUrl = (string) config('services.bmi.request_url');
        $this->purchaseUrl = (string) config('services.bmi.purchase_url');
        $this->verifyUrl = (string) config('services.bmi.verify_url');
    }

    public function requestPayment(string $orderId, int $amount, string $returnUrl): array
    {
        $amount=10000;
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'مبلغ پرداخت نامعتبر است.',
            ];
        }

        if ($this->terminalId === '' || $this->merchantId === '' || $this->key === '') {
            return [
                'success' => false,
                'message' => 'تنظیمات درگاه بانک ملی کامل نیست.',
            ];
        }
        //dd($this->terminalId , $orderId , $amount);

        $signData = $this->encryptPkcs7("{$this->terminalId};{$orderId};{$amount}");

        //dd($signData);

        if ($signData === null) {
            return [
                'success' => false,
                'message' => 'تولید امضای دیجیتال ناموفق بود.',
            ];
        }

        $payload = [
            'TerminalId' => $this->terminalId,
            'MerchantId' => $this->merchantId,
            'Amount' => $amount,
            'SignData' => $signData,
            'ReturnUrl' => $returnUrl,
            'LocalDateTime' => date('m/d/Y g:i:s a'),
            'OrderId' => $orderId,
        ];

        $result = $this->callApi($this->requestUrl, $payload);

        //dd($result);
        if (!$result) {
            return [
                'success' => false,
                'message' => 'پاسخی از درگاه دریافت نشد.',
            ];
        }

        if ((int) ($result['ResCode'] ?? -1) !== 0) {
            return [
                'success' => false,
                'message' => (string) ($result['Description'] ?? 'درخواست پرداخت ناموفق بود.'),
                'res_code' => (int) ($result['ResCode'] ?? -1),
            ];
        }

        $token = (string) ($result['Token'] ?? '');
        if ($token === '') {
            return [
                'success' => false,
                'message' => 'توکن پرداخت دریافت نشد.',
            ];
        }

        return [
            'success' => true,
            'token' => $token,
            'redirect_url' => "{$this->purchaseUrl}?Token={$token}",
        ];
    }

    public function verifyPayment(string $token): array
    {
        if ($token === '') {
            return [
                'success' => false,
                'message' => 'توکن تایید پرداخت نامعتبر است.',
            ];
        }

        $signData = $this->encryptPkcs7($token);
        if ($signData === null) {
            return [
                'success' => false,
                'message' => 'تولید امضای تایید پرداخت ناموفق بود.',
            ];
        }

        $payload = [
            'Token' => $token,
            'SignData' => $signData,
        ];

        $result = $this->callApi($this->verifyUrl, $payload);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'پاسخی از سرویس Verify دریافت نشد.',
            ];
        }

        $resCode = (int) ($result['ResCode'] ?? -1);

        return [
            'success' => $resCode === 0,
            'res_code' => $resCode,
            'message' => (string) ($result['Description'] ?? ''),
            'retrival_ref_no' => (string) ($result['RetrivalRefNo'] ?? ''),
            'system_trace_no' => (string) ($result['SystemTraceNo'] ?? ''),
            'order_id' => (string) ($result['OrderId'] ?? ''),
            'raw' => $result,
        ];
    }

    private function encryptPkcs7(string $data): ?string
    {
       // dd($this->key);
        $decodedKey = base64_decode($this->key, true);
        if ($decodedKey === false) {
            return null;
        }

        $ciphertext = openssl_encrypt($data, 'DES-EDE3', $decodedKey, OPENSSL_RAW_DATA);
        if ($ciphertext === false) {
            return null;
        }

        return base64_encode($ciphertext);
    }

    private function callApi(string $url, array $payload): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8',
            ])->withOptions([
                'verify' => $this->verifySsl,
            ])->post($url, $payload);

            if (!$response->ok()) {
                return null;
            }

            $json = $response->json();
            return is_array($json) ? $json : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
