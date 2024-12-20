<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Commands;

use Modules\Invoices\Domain\ValueObjects\InvoiceId;

readonly class SendInvoiceCommand
{
    public function __construct(
        public InvoiceId $id
    ) {}
}
