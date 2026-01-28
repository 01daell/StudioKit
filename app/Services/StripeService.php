<?php
namespace App\Services;

use App\Core\Config;

class StripeService
{
    private string $secret;

    public function __construct()
    {
        $this->secret = Config::get('stripe.secret_key', '');
    }

    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->secret . ':',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return ['error' => $error];
        }
        $decoded = json_decode($response, true);
        return $decoded ?? ['error' => 'Invalid response'];
    }

    public function createCheckoutSession(array $data): array
    {
        return $this->request('POST', 'checkout/sessions', $data);
    }

    public function createCustomerPortal(string $customerId, string $returnUrl): array
    {
        return $this->request('POST', 'billing_portal/sessions', [
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);
    }

    public function verifyWebhook(string $payload, string $sigHeader, string $secret): ?array
    {
        $parts = explode(',', $sigHeader);
        $timestamp = null;
        $signature = null;
        foreach ($parts as $part) {
            [$key, $value] = array_map('trim', explode('=', $part, 2));
            if ($key === 't') {
                $timestamp = $value;
            }
            if ($key === 'v1') {
                $signature = $value;
            }
        }
        if (!$timestamp || !$signature) {
            return null;
        }
        $signedPayload = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);
        if (!hash_equals($expected, $signature)) {
            return null;
        }
        return json_decode($payload, true);
    }
}
