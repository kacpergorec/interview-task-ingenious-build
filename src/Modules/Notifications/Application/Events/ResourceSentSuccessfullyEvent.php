<?php

declare(strict_types=1);

namespace Modules\Notifications\Application\Events;

use Symfony\Component\Uid\Uuid;

final readonly class ResourceSentSuccessfullyEvent
{
    public function __construct(
        public Uuid $resourceId,
    ) {}
}
