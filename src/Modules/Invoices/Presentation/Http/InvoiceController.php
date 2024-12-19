<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Notifications\Application\Services\NotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final readonly class InvoiceController
{

    public function show(string $id, GetInvoiceHandler $handler): JsonResponse
    {
        $id = InvoiceId::fromString($id);

        return new JsonResponse(
            $handler->handle(new GetInvoiceQuery($id))
        );
    }

    public function store(Request $request): JsonResponse
    {
        dd($request->all());
        return new JsonResponse('todo store');
    }

    public function send(): JsonResponse
    {
        return new JsonResponse('todo send');
    }
}
