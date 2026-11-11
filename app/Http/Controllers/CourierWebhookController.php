<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourierWebhookController extends Controller
{
    /**
     * Handle incoming webhook notifications from Steadfast Courier
     * 
     * Webhook URL: /api/courier/webhook/steadfast
     */
    public function handleSteadfast(Request $request): JsonResponse
    {
        // Friendly health-check response when visited directly in a web browser (GET)
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'active',
                'service' => 'Steadfast Courier Real-Time Webhook',
                'supported_method' => 'POST',
                'message' => 'Steadfast Webhook এন্ডপয়েন্ট সক্রিয় রয়েছে। Steadfast মার্চেন্ট সার্ভার থেকে স্বয়ংক্রিয় POST রিকোয়েস্ট গ্রহণের জন্য প্রস্তুত।'
            ]);
        }

        Log::info('Steadfast Webhook Received: ', $request->all());

        $consignmentId = $request->input('consignment_id') ?? $request->input('consignmentId') ?? $request->input('cid');
        $trackingCode = $request->input('tracking_code') ?? $request->input('trackingCode');
        $invoice = $request->input('invoice') ?? $request->input('order_id') ?? $request->input('invoice_id');
        $rawStatus = strtolower(trim((string) ($request->input('status') ?? $request->input('delivery_status') ?? '')));

        if (empty($rawStatus)) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'No status provided in webhook payload.'
            ], 200);
        }

        // Find the matching order
        $order = $this->findOrder($consignmentId, $trackingCode, $invoice);

        if (!$order) {
            Log::warning("Steadfast Webhook: Order not found for consignment: {$consignmentId}, tracking: {$trackingCode}, invoice: {$invoice}");
            return response()->json([
                'status' => 'ignored',
                'message' => 'Matching order not found in database.'
            ], 200);
        }

        return $this->processStatusUpdate($order, 'steadfast', $rawStatus, $request->all());
    }

    /**
     * Handle incoming webhook notifications from Pathao Courier
     * 
     * Webhook URL: /api/courier/webhook/pathao
     */
    public function handlePathao(Request $request): JsonResponse
    {
        // Friendly health-check response when visited directly in a web browser (GET)
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'active',
                'service' => 'Pathao Courier Real-Time Webhook',
                'supported_method' => 'POST',
                'message' => 'Pathao Webhook এন্ডপয়েন্ট সক্রিয় রয়েছে। Pathao সার্ভার থেকে স্বয়ংক্রিয় POST রিকোয়েস্ট গ্রহণের জন্য প্রস্তুত।'
            ]);
        }

        Log::info('Pathao Webhook Received: ', $request->all());

        $event = strtolower(trim((string) $request->input('event', '')));
        $consignmentId = $request->input('consignment_id');
        $merchantOrderId = $request->input('merchant_order_id');
        $orderStatus = strtolower(trim((string) $request->input('order_status', '')));

        $rawStatus = $orderStatus ?: $event;

        if (empty($rawStatus)) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'No status provided in Pathao webhook payload.'
            ], 200);
        }

        $order = $this->findOrder($consignmentId, null, $merchantOrderId);

        if (!$order) {
            Log::warning("Pathao Webhook: Order not found for consignment: {$consignmentId}, merchant_order_id: {$merchantOrderId}");
            return response()->json([
                'status' => 'ignored',
                'message' => 'Matching order not found in database.'
            ], 200);
        }

        return $this->processStatusUpdate($order, 'pathao', $rawStatus, $request->all());
    }

    /**
     * Find an order matching any of the available courier identifiers
     */
    protected function findOrder(?string $consignmentId, ?string $trackingCode, ?string $invoice): ?Order
    {
        $query = Order::query();

        $query->where(function ($q) use ($consignmentId, $trackingCode, $invoice) {
            $hasCondition = false;

            if (!empty($consignmentId)) {
                $q->orWhere('courier_consignment_id', (string) $consignmentId);
                $hasCondition = true;
            }
            if (!empty($trackingCode)) {
                $q->orWhere('courier_tracking_code', (string) $trackingCode);
                $hasCondition = true;
            }
            if (!empty($invoice)) {
                $q->orWhere('order_number', (string) $invoice);
                $hasCondition = true;
            }

            if (!$hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });

        return $query->first();
    }

    /**
     * Process order status transition, payment update, restocking, audit logging and SMS alerts
     */
    protected function processStatusUpdate(Order $order, string $courierName, string $rawStatus, array $payload): JsonResponse
    {
        $oldStatus = $order->order_status;
        $mappedStatus = $this->mapCourierStatusToLocal($courierName, $rawStatus);

        $order->courier_status = $rawStatus;
        $courierDisplayName = ucfirst($courierName);
        $timestamp = now()->format('d M Y, h:i A');

        // Append audit trail note
        $auditEntry = "[{$timestamp}] {$courierDisplayName} Webhook: কুরিয়ার স্ট্যাটাস '{$rawStatus}' পাওয়া গেছে।";

        if ($mappedStatus && $mappedStatus !== $oldStatus) {
            $order->order_status = $mappedStatus;
            $auditEntry .= " সিস্টেম স্ট্যাটাস '{$oldStatus}' থেকে '{$mappedStatus}'-এ আপডেট হয়েছে।";

            // 1. If delivered, mark Cash On Delivery as paid
            if ($mappedStatus === 'delivered' && $order->payment_method === 'cod') {
                $order->payment_status = 'paid';
                $auditEntry .= " (পেমেন্ট স্ট্যাটাস: Paid)";
            }

            // 2. If cancelled or returned, restock inventory if not already cancelled
            if ($mappedStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restockItems($order);
                $auditEntry .= " (পণ্য ইনভেন্টরিতে পুনরায় যোগ করা হয়েছে)";
            }

            // 3. Trigger SMS Notification if configured
            try {
                app(SmsService::class)->sendOrderNotification($order, $mappedStatus);
            } catch (\Throwable $e) {
                Log::error("Courier Webhook SMS Trigger Error for Order #{$order->order_number}: " . $e->getMessage());
            }
        }

        // Save admin notes without wiping previous notes
        $existingNotes = $order->admin_notes ? trim($order->admin_notes) . "\n" : '';
        $order->admin_notes = $existingNotes . $auditEntry;
        $order->save();

        Log::info("Courier Webhook Success: Order #{$order->order_number} status is now '{$order->order_status}' (Courier: {$rawStatus})");

        return response()->json([
            'status' => 'success',
            'order_number' => $order->order_number,
            'order_status' => $order->order_status,
            'courier_status' => $order->courier_status,
            'payment_status' => $order->payment_status,
            'message' => "Order #{$order->order_number} updated successfully."
        ], 200);
    }

    /**
     * Map third-party courier statuses to standard local order statuses
     */
    protected function mapCourierStatusToLocal(string $courierName, string $rawStatus): ?string
    {
        $status = strtolower(trim($rawStatus));

        // Steadfast status mapping
        if ($courierName === 'steadfast') {
            return match ($status) {
                'delivered', 'partial_delivered' => 'delivered',
                'cancelled', 'cancelled_by_customer', 'cancelled_by_recipient', 'return', 'returned', 'lost', 'damaged' => 'cancelled',
                'delivered_approval_pending', 'out_for_delivery', 'in_transit', 'picked', 'in_courier' => 'shipped',
                'hold' => 'shipped', // Remains in courier transit
                'in_review', 'pending' => null, // Keep existing status
                default => null,
            };
        }

        // Pathao status mapping
        if ($courierName === 'pathao') {
            if (str_contains($status, 'delivered')) {
                return 'delivered';
            }
            if (str_contains($status, 'returned') || str_contains($status, 'cancelled')) {
                return 'cancelled';
            }
            if (str_contains($status, 'transit') || str_contains($status, 'picked') || str_contains($status, 'dispatched')) {
                return 'shipped';
            }
        }

        // Generic fallback
        if (str_contains($status, 'deliver')) {
            return 'delivered';
        }
        if (str_contains($status, 'cancel') || str_contains($status, 'return')) {
            return 'cancelled';
        }

        return null;
    }

    /**
     * Restock order items back into inventory
     */
    protected function restockItems(Order $order): void
    {
        foreach ($order->items as $item) {
            if (!empty($item->variant_id)) {
                $variant = ProductVariant::find($item->variant_id);
                if ($variant) {
                    $variant->increment('stock_quantity', (int) $item->quantity);
                }
            }
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock_quantity', (int) $item->quantity);
            }
        }
    }
}
