<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\QueryHandlers;

use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;

readonly class GetInvoiceHandler
{
    public function __construct(
        private InvoiceQueryRepositoryInterface $repository,
    ) {}

    public function handle(GetInvoiceQuery $query): ?Invoice
    {
        return $this->repository->find($query->invoiceId);
    }
}
