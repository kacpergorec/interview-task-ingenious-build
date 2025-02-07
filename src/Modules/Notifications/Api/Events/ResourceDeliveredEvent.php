<?php

declare(strict_types=1);

namespace Modules\Notifications\Api\Events;

use Symfony\Component\Uid\Uuid;

final readonly class ResourceDeliveredEvent
{
    public function __construct(
        public Uuid $resourceId,
    ) {}
}
