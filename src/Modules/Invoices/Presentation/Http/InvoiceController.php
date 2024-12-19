<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Api\Factory\InvoiceDtoFactory;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Symfony\Component\HttpFoundation\Response;

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
        $dto = InvoiceDtoFactory::fromRequest($request);
        Event::dispatch(new InvoiceCreationRequestEvent($dto));

        return new JsonResponse($dto->id, Response::HTTP_ACCEPTED);
    }

    public function send(): JsonResponse
    {
        return new JsonResponse('todo send');
    }
}
