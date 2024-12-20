<?php

declare(strict_types=1);

namespace Modules\Notifications\Infrastructure\Drivers;


use Illuminate\Support\Facades\Log;

class DummyDriver implements DriverInterface
{
    public function send(
        string $toEmail,
        string $subject,
        string $message,
        string $reference,
    ): bool {
        try {
            // ... send email
            return true;
        }catch (\Exception $e) {
            Log::error('Failed to send email', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
