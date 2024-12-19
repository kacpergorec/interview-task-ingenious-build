<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Dtos;

use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Shared\Domain\ValueObject\Money;

readonly class InvoiceProductLineDto
{
    public function __construct(
        public InvoiceProductLineId $id,
        public int                  $quantity,
        public string               $name,
        public Money                $price
    )
    {
    }
}
