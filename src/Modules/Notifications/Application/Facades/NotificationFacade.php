<?php

declare(strict_types=1);

namespace Modules\Notifications\Application\Facades;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Notifications\Application\Events\ResourceSentSuccessfullyEvent;
use Modules\Notifications\Infrastructure\Drivers\DriverInterface;

final readonly class NotificationFacade implements NotificationFacadeInterface
{
    public function __construct(
        private DriverInterface $driver,
    )
    {
    }

    public function notify(NotifyData $data): void
    {
        $success = $this->driver->send(
            toEmail: $data->toEmail,
            subject: $data->subject,
            message: $data->message,
            reference: $data->resourceId->toString(),
        );

        if ($success) {
            Event::dispatch(new ResourceSentSuccessfullyEvent($data->resourceId));

            // ... time elapses

            //  simulate external webhook call
            $webHook = route('notification.hook', [
                'action' => 'delivered',
                'reference' => $data->resourceId->toString(),
            ]);
            Http::get($webHook);
            return;
        }

        // Handle failure, allowing for possible retry
        // Event::dispatch(new ResourceSendingFailedEvent($data->resourceId));
    }
}
