<?php
declare (strict_types=1);

namespace Modules\Invoices\Api\Events;

use Modules\Invoices\Domain\ValueObjects\InvoiceId;

class InvoiceSendRequestEvent
{
    public function __construct(
        public InvoiceId $invoiceId
    ) {}
}
