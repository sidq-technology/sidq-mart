<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Services\Courier\Drivers\PaperflyDriver;
use App\Services\Courier\Drivers\PathaoDriver;
use App\Services\Courier\Drivers\RedxDriver;
use App\Services\Courier\Drivers\SteadfastDriver;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class CourierManager
{
    /**
     * Map of available driver classes
     */
    protected array $driverClasses = [
        'steadfast' => SteadfastDriver::class,
        'pathao' => PathaoDriver::class,
        'paperfly' => PaperflyDriver::class,
        'redx' => RedxDriver::class,
    ];

    /**
     * Instantiated drivers cache
     */
    protected array $drivers = [];

    /**
     * Resolve a driver instance by name
     */
    public function driver(string $name): CourierInterface
    {
        $name = strtolower(trim($name));

        if (!isset($this->driverClasses[$name])) {
            throw new \InvalidArgumentException("অজানা কুরিয়ার সার্ভিস: '{$name}'");
        }

        if (!isset($this->drivers[$name])) {
            $class = $this->driverClasses[$name];
            $this->drivers[$name] = new $class();
        }

        return $this->drivers[$name];
    }

    /**
     * Get all couriers that are currently enabled in Admin Settings
     */
    public function getEnabledCouriers(): array
    {
        $enabled = [];
        $meta = [
            'steadfast' => ['badge_class' => 'bg-danger text-white', 'icon' => 'fas fa-bolt'],
            'pathao' => ['badge_class' => 'bg-danger text-white', 'icon' => 'fas fa-motorcycle'],
            'paperfly' => ['badge_class' => 'bg-primary text-white', 'icon' => 'fas fa-paper-plane'],
            'redx' => ['badge_class' => 'bg-danger text-white', 'icon' => 'fas fa-truck'],
        ];

        foreach ($this->driverClasses as $key => $class) {
            $driver = $this->driver($key);
            if ($driver->isEnabled()) {
                $enabled[$key] = [
                    'key' => $key,
                    'name' => $driver->getName(),
                    'label' => $driver->getLabel(),
                    'icon' => $meta[$key]['icon'] ?? 'fas fa-truck',
                    'badge_class' => $meta[$key]['badge_class'] ?? 'bg-secondary text-white',
                ];
            }
        }

        return $enabled;
    }

    /**
     * Send an order to the selected courier
     */
    public function sendOrder(Order $order, string $courierName): array
    {
        $driver = $this->driver($courierName);

        if (!$driver->isEnabled()) {
            return [
                'success' => false,
                'message' => "কুরিয়ার '{$driver->getLabel()}' চালু করা হয়নি বা ক্রেডেনশিয়াল দেওয়া হয়নি। Admin > Integrations এ সেটিংস চেক করুন।"
            ];
        }

        $result = $driver->sendOrder($order);

        if ($result['success']) {
            $oldStatus = $order->order_status;
            $newStatus = in_array($oldStatus, ['pending', 'processing']) ? 'shipped' : $oldStatus;

            $order->update([
                'courier_name' => $result['courier'],
                'courier_tracking_code' => $result['tracking_code'],
                'courier_consignment_id' => $result['consignment_id'],
                'courier_status' => $result['status'] ?? 'booked',
                'courier_submitted_at' => now(),
                'order_status' => $newStatus,
            ]);

            // Trigger SMS notification if status changed to shipped
            if ($oldStatus !== $newStatus) {
                try {
                    app(SmsService::class)->sendOrderNotification($order, $newStatus);
                } catch (\Throwable $e) {
                    Log::error("Courier booking SMS Trigger Error: " . $e->getMessage());
                }
            }

            $result['order_status'] = $newStatus;
            $result['status_badge_class'] = $order->status_badge_class;
            $result['tracking_url'] = $order->courier_tracking_url;
        }

        return $result;
    }

    /**
     * Test credentials for a specific courier
     */
    public function testDriver(string $courierName): array
    {
        $driver = $this->driver($courierName);
        return $driver->testCredentials();
    }
}
