<?php

namespace App\Jobs;

use App\Services\MetaCapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMetaCapiEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 10;

    protected string $eventName;
    protected string $eventId;
    protected array $userData;
    protected array $customData;
    protected ?string $eventSourceUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $eventName,
        string $eventId,
        array $userData = [],
        array $customData = [],
        ?string $eventSourceUrl = null
    ) {
        $this->eventName = $eventName;
        $this->eventId = $eventId;
        $this->userData = $userData;
        $this->customData = $customData;
        $this->eventSourceUrl = $eventSourceUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(MetaCapiService $capiService): void
    {
        try {
            $capiService->sendEvent(
                $this->eventName,
                $this->eventId,
                $this->userData,
                $this->customData,
                $this->eventSourceUrl
            );
        } catch (\Throwable $e) {
            Log::warning("SendMetaCapiEvent job failed for {$this->eventName}: " . $e->getMessage());
        }
    }
}
