<?php
declare (strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Shared\Domain\ValueObject\Money;

class InvoiceProductLine
{
    private Money $totalUnitPrice;

    public function __construct(
        public readonly InvoiceProductLineId $id,
        public readonly string               $name,
        public readonly int                  $quantity,
        public readonly Money                $price,
    )
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        $this->recalculateTotalUnitPrice();
    }

    public static function recreate(
        InvoiceProductLineId $id,
        string               $name,
        int                  $quantity,
        Money                $price,
        Money                $totalUnitPrice,
    ): self
    {
        $line = new self(
            id: $id,
            name: $name,
            quantity: $quantity,
            price: $price,
        );

        $line->totalUnitPrice = $totalUnitPrice;

        return $line;
    }

    public function getTotalUnitPrice(): Money
    {
        return $this->totalUnitPrice;
    }

    private function recalculateTotalUnitPrice(): void
    {
        $this->totalUnitPrice = $this->price->multiply($this->quantity);
    }
}
