<?php

namespace App\Service;

use App\Models\DompulChip;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Sidompul
{



    /**
     * @var DompulChip
     */
    private $chip;


    public function __construct(DompulChip $chip)
    {
        $this->chip = $chip;
        $this->getToken();
    }

    public function updatePin(string $newPin)
    {
        $result = $this->encrypt($newPin);
        $this->chip->update(['pin' => $result['result']['data']]);
    }

    public function exec(string $msisdn, string $product_code)
    {
        $w2p_denom = [
            5000,
            10000,
            25000,
            50000,
            100000,
            150000,
            200000,
            300000,
            500000
        ];

        if (in_array($product_code,$w2p_denom))
        {
            return $this->reloadW2P($msisdn, $product_code);
        } else {
            return $this->injectPackage($msisdn, $product_code);
        }



    }

    private function reloadW2P(string $msisdn, int $denomination)
    {
        $url = 'https://gateway.egw.xl.co.id/sidompul/openapi/v1/post-w2p';
        $headers = $this->getHeaders($url);

        $body = [
            'msisdn'=>$msisdn,
            'pin'=>$this->chip->pin,
            'denom'=>$denomination
        ];

        $api = Http::withToken($this->chip->access_token)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        Transaction::create([
            'dompul_chip_id' => $this->chip->id,
            'msisdn' => $msisdn,
            'product_code' => $denomination,
            'status' => $api['result']['errorMessage'] ?? null,
            'transaction_id' => $api['result']['data']['transactionId'] ?? null,
            'description' => $api['result']['data']['description'] ?? null,
            'raw' => $api,
        ]);

        return $api;
    }

    private function injectPackage(string $msisdn, string $productCode)
    {
        $url = 'https://gateway.egw.xl.co.id/sidompul/openapi/v1/post-package';
        $headers = $this->getHeaders($url);

        $body = [
            'msisdn'=>$msisdn,
            'pin'=>$this->chip->pin,
            'productCode'=>$productCode
        ];

        $api = Http::withToken($this->chip->access_token)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        Transaction::create([
            'dompul_chip_id' => $this->chip->id,
            'msisdn' => $msisdn,
            'product_code' => $productCode,
            'transaction_id' => null,
            'status' => $api['result']['errorMessage'] ?? null,
            'description' => $api['result']['data']['description'] ?? null,
            'raw' => $api,
        ]);

        return $api;
    }

    public function TransactionHistory(string $period = 'today')
    {
        $url = 'https://gateway.egw.xl.co.id/sidompul/openapi/v1/get-transaction-history';
        $headers = $this->getHeaders($url);

        return Http::withToken($this->chip->access_token)
            ->withHeaders($headers)
            ->get($url,['type'=>$period]);
    }

    private function getToken(): void
    {
        if (Carbon::now() >= $this->chip->token_expired_at)
            $this->generateNewToken();
    }

    private function generateNewToken(): void
    {
        $body = [
            'client_id' => $this->chip->client_id,
            'client_secret' => $this->chip->client_secret,
            'grant_type' => 'client_credentials'

        ];
        $result = Http::asForm()->post('https://gateway.egw.xl.co.id/token', $body);

        if ($result->successful()) {
            $token = $result['access_token'];
            $expire = Carbon::now()->addHours(1);
            $this->updateToken($token, $expire);
        }
    }

    private function updateToken($token, $expire): void
    {
        $this->chip->update([
            'access_token' => $token,
            'token_expired_at' => $expire
        ]);
    }

    private function encrypt(string $newPin): object
    {
        $result = Http::withToken($this->chip->access_token)->asJson()->post('https://gateway.egw.xl.co.id/sidompul/openapi/v1/post-encrypt',
            ['data' => $newPin]);
        return $result;
    }

    private function getHeaders(string $url): array
    {
        $api_guard = $this->chip->endpoints()->where('url', $url)->first();
        $headers = [
            'apiid' => $api_guard->api_id,
            'apikey' => $api_guard->api_key,
            'language' => 'ID'
        ];
        return $headers;
    }

}
