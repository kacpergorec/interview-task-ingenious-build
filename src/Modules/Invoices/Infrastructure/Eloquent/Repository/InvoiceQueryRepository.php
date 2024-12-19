<?php
declare (strict_types=1);

namespace Modules\Invoices\Infrastructure\Eloquent\Repository;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Infrastructure\Eloquent\Mapper\InvoiceMapper;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;

class InvoiceQueryRepository implements InvoiceQueryRepositoryInterface
{
    public function find(InvoiceId $invoiceId): ?Invoice
    {
        $eloquentInvoice = InvoiceModel::with('invoiceLines')->find($invoiceId);

        if ($eloquentInvoice === null) {
            return null;
        }

        return InvoiceMapper::fromEloquent($eloquentInvoice);
    }
}
