<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Commands;

use Modules\Invoices\Application\Dtos\InvoiceDto;

readonly class CreateInvoiceCommand
{
    public function __construct(
        public InvoiceDto $dto
    ) {}
}
