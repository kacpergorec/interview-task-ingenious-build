<?php
declare (strict_types=1);

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;

interface InvoiceQueryRepositoryInterface
{
    public function find(InvoiceId $invoiceId): ?Invoice;
}
