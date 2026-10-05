<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 25;

    public function __construct(
        public string $recipient,
        public string $message,
        public string $purpose = 'transactional',
        public ?int $orderId = null,
        public ?int $logId = null
    ) {}

    public function handle(SmsService $smsService): void
    {
        try {
            $response = $smsService->executeSend(
                $this->recipient,
                $this->message,
                $this->purpose,
                $this->orderId,
                $this->logId
            );

            Log::info("SMS dispatched successfully to {$this->recipient}. Response: " . json_encode($response));
        } catch (\Throwable $e) {
            Log::error("Failed to execute SendSmsJob for {$this->recipient}: " . $e->getMessage());

            if ($this->logId) {
                SmsLog::where('id', $this->logId)->update([
                    'status' => 'failed',
                    'response_data' => 'Job Exception: ' . $e->getMessage(),
                ]);
            }
        }
    }
}
