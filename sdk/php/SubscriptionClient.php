<?php

namespace SubscriptionManager\SDK;

/**
 * Subscription Management System PHP SDK
 * 
 * This SDK allows external applications to integrate with the Subscription Management System
 * 
 * Usage:
 * $client = new SubscriptionClient('https://subscription-system.com', 'your-api-key', 'your-api-secret');
 * $userSubscriptions = $client->getUserSubscriptions(123);
 * $hasAccess = $client->checkUserAccess(123, 456);
 */
class SubscriptionClient
{
    private string $baseUrl;
    private string $apiKey;
    private string $apiSecret;
    private ?string $token = null;
    private int $tokenExpiry = 0;

    public function __construct(string $baseUrl, string $apiKey, string $apiSecret)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Get all subscriptions for a user in this application
     */
    public function getUserSubscriptions(int $userId): array
    {
        return $this->makeRequest('GET', "/api/users/{$userId}/subscriptions");
    }

    /**
     * Check if a user has access to a specific plan
     */
    public function checkUserAccess(int $userId, int $planId): bool
    {
        $response = $this->makeRequest('GET', "/api/users/{$userId}/access/{$planId}");
        return $response['has_access'] ?? false;
    }

    /**
     * Get all available plans for this application
     */
    public function getAvailablePlans(): array
    {
        $response = $this->makeRequest('GET', '/api/plans');
        return $response['plans'] ?? [];
    }

    /**
     * Create a new subscription for a user
     */
    public function createSubscription(int $userId, int $planId): array
    {
        return $this->makeRequest('POST', '/api/subscriptions', [
            'user_id' => $userId,
            'plan_id' => $planId,
        ]);
    }

    /**
     * Get application statistics
     */
    public function getApplicationStats(): array
    {
        $response = $this->makeRequest('GET', '/api/stats');
        return $response['stats'] ?? [];
    }

    /**
     * Authenticate and get access token
     */
    private function authenticate(): bool
    {
        if ($this->token && time() < $this->tokenExpiry) {
            return true;
        }

        $response = $this->makeRequest('POST', '/api/auth', [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ], false);

        if (isset($response['token'])) {
            $this->token = $response['token'];
            $this->tokenExpiry = time() + (23 * 3600); // Expire 1 hour before actual expiry
            return true;
        }

        return false;
    }

    /**
     * Make HTTP request to the API
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], bool $requireAuth = true): array
    {
        if ($requireAuth && !$this->authenticate()) {
            throw new \Exception('Authentication failed');
        }

        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($requireAuth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false) {
            throw new \Exception('Failed to make HTTP request');
        }

        $decodedResponse = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new \Exception($decodedResponse['message'] ?? 'API request failed', $httpCode);
        }

        return $decodedResponse;
    }

    /**
     * Verify if a user has any active subscription for this application
     */
    public function hasActiveSubscription(int $userId): bool
    {
        $subscriptions = $this->getUserSubscriptions($userId);
        
        if (!isset($subscriptions['subscriptions'])) {
            return false;
        }

        foreach ($subscriptions['subscriptions'] as $subscription) {
            if ($subscription['is_active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user's active subscription plans
     */
    public function getUserActivePlans(int $userId): array
    {
        $subscriptions = $this->getUserSubscriptions($userId);
        $activePlans = [];

        if (!isset($subscriptions['subscriptions'])) {
            return $activePlans;
        }

        foreach ($subscriptions['subscriptions'] as $subscription) {
            if ($subscription['is_active']) {
                $activePlans[] = $subscription['plan'];
            }
        }

        return $activePlans;
    }

    /**
     * Check if user has access to a specific feature based on their subscription plans
     */
    public function hasFeatureAccess(int $userId, string $feature): bool
    {
        $activePlans = $this->getUserActivePlans($userId);

        foreach ($activePlans as $plan) {
            if (isset($plan['features']) && in_array($feature, $plan['features'])) {
                return true;
            }
        }

        return false;
    }
}