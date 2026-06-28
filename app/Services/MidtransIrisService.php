<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransIrisService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        // Midtrans Iris secara ketat membutuhkan Approver Key.
        $this->apiKey = config('services.midtrans.iris_api_key') ?? config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production');
        
        $this->baseUrl = $isProduction 
            ? 'https://app.midtrans.com/iris/api/v1' 
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
    }

    /**
     * Get Iris headers
     */
    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':')
        ];
    }

    /**
     * Check Balance
     */
    public function getBalance()
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/balance");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Midtrans Iris getBalance error: ' . $response->body());
        return null;
    }

    /**
     * Create Payouts
     * 
     * @param array $payoutData Data of the payout
     * @return array|null
     */
    public function createPayout(array $payoutData)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/payouts", [
                'payouts' => [$payoutData]
            ]);

        if ($response->successful() || $response->status() === 201) {
            return $response->json();
        }

        Log::error('Midtrans Iris createPayout error: ' . $response->body());
        
        // Return structured error
        return [
            'error' => true,
            'message' => $response->body(),
            'status' => $response->status()
        ];
    }

    /**
     * Check payout details
     */
    public function checkPayoutStatus(string $referenceNo)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/payouts/{$referenceNo}");

        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error("Midtrans Iris checkPayoutStatus error ($referenceNo): " . $response->body());
        return null;
    }
}
