<?php
declare (strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Events\InvoiceCreatedEvent;
use Modules\Invoices\Domain\Events\InvoiceSendingEvent;
use Modules\Invoices\Domain\Events\InvoiceSentEvent;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Shared\Domain\AggregateRoot;
use Shared\Domain\ValueObject\Money;

class Invoice extends AggregateRoot
{
    private Money $totalPrice;

    private function __construct(
        public readonly InvoiceId    $id,
        public readonly CustomerInfo $customerInfo,

        /** @var InvoiceProductLine[] */
        private array                 $invoiceProductLines = [],
        private StatusEnum            $status = StatusEnum::Draft,
    )
    {
        $this->totalPrice = new Money(0);
    }

    public static function create(
        InvoiceId    $id,
        CustomerInfo $customerInfo,
    ): self
    {
        $invoice = new self(
            id: $id,
            customerInfo: $customerInfo,
            status: StatusEnum::Draft,
        );

         $invoice->raise(new InvoiceCreatedEvent($id));

        return $invoice;
    }

    public static function recreate(
        InvoiceId    $id,
        CustomerInfo $customerInfo,
        array        $invoiceProductLines,
        StatusEnum   $status,
        Money        $totalPrice,
    ): Invoice
    {
        $invoice = new self(
            id: $id,
            customerInfo: $customerInfo,
            invoiceProductLines: $invoiceProductLines,
            status: $status,
        );

        $invoice->invoiceProductLines = $invoiceProductLines;
        $invoice->totalPrice = $totalPrice;

        return $invoice;
    }

    public function addInvoiceProductLine(
        InvoiceProductLineId $productLineId,
        string               $name,
        int                  $quantity,
        Money                $unitPrice,
    ): void
    {
        $this->invoiceProductLines[] = new InvoiceProductLine(
            id: $productLineId,
            name: $name,
            quantity: $quantity,
            price: $unitPrice,
        );

        $this->recalculateTotalPrice();
    }


    private function recalculateTotalPrice(): void
    {
        $this->totalPrice = new Money(0);

        foreach ($this->invoiceProductLines as $productLine) {
            $this->totalPrice = $this->totalPrice->add($productLine->getTotalUnitPrice());
        }
    }

    public function send(): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw new \DomainException("Cannot send an invoice that is not in draft status");
        }

        $this->status = StatusEnum::Sending;

        $this->raise(new InvoiceSendingEvent($this->id));
    }

    public function markAsSent(): void
    {
        if ($this->status !== StatusEnum::Sending) {
            throw new \DomainException("Cannot mark invoice as sent without first initiating send");
        }

        $this->status = StatusEnum::SentToClient;

        $this->raise(new InvoiceSentEvent($this->id));
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    /** @return InvoiceProductLine[] */
    public function getInvoiceProductLines(): array
    {
        return $this->invoiceProductLines;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function toArray(): array
    {
        $productLines = array_map(
            fn(InvoiceProductLine $productLine) => [
                'id' => $productLine->id->__toString(),
                'name' => $productLine->name,
                'quantity' => $productLine->quantity,
                'price' => $productLine->price->value,
                'totalPrice' => $productLine->getTotalUnitPrice()->value,
            ],
            $this->invoiceProductLines
        );

        return [
            'id' => $this->id->__toString(),
            'customerInfo' => $this->customerInfo,
            'invoiceProductLines' => $productLines,
            'status' => $this->status->value,
            'totalPrice' => $this->totalPrice->value,
        ];
    }
}
