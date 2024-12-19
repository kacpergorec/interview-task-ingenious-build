<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Queries;

use Modules\Invoices\Domain\ValueObjects\InvoiceId;

final readonly class GetInvoiceQuery
{
    public function __construct(
        public InvoiceId $invoiceId,
    ) {}
}
