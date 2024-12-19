<?php
declare (strict_types=1);

namespace Modules\Invoices\Domain\Events;

use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Shared\Domain\DomainEventInterface;

readonly class InvoiceCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public InvoiceId $invoiceId
    ) {}
}
