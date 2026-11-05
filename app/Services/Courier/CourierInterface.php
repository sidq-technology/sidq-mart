<?php

namespace App\Services\Courier;

use App\Models\Order;

interface CourierInterface
{
    /**
     * Unique identifier for the courier (e.g. 'steadfast', 'pathao')
     */
    public function getName(): string;

    /**
     * Human-readable label for the UI (e.g. 'Steadfast Courier (স্টেডফাস্ট)')
     */
    public function getLabel(): string;

    /**
     * Check if this courier is enabled and has credentials filled in Admin Settings
     */
    public function isEnabled(): bool;

    /**
     * Send an order/parcel to the courier API
     * Returns ['success' => bool, 'tracking_code' => string, 'consignment_id' => string, 'message' => string]
     */
    public function sendOrder(Order $order): array;

    /**
     * Test connection/credentials
     * Returns ['success' => bool, 'message' => string]
     */
    public function testCredentials(): array;
}
