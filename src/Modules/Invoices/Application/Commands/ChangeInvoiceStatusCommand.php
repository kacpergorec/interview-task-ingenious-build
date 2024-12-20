<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Commands;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;

class ChangeInvoiceStatusCommand
{
    public function __construct(
        public InvoiceId $id,
        public StatusEnum $status
    ) {}
}
