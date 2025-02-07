<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Api\Events\InvoiceSendRequestEvent;
use Modules\Invoices\Api\Factory\InvoiceDtoFactory;
use Modules\Invoices\Application\CommandHandlers\SendInvoiceHandler;
use Modules\Invoices\Application\Commands\SendInvoiceCommand;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Symfony\Component\HttpFoundation\Response;

final readonly class InvoiceController
{
    public function show(string $id, GetInvoiceHandler $handler): JsonResponse
    {
        $id = InvoiceId::fromString($id);
        $invoice = $handler->handle(new GetInvoiceQuery($id));

        return new JsonResponse(
            $invoice, $invoice ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request): JsonResponse
    {
        $dto = InvoiceDtoFactory::fromRequest($request);
        Event::dispatch(new InvoiceCreationRequestEvent($dto));

        return new JsonResponse($dto->id, Response::HTTP_CREATED);
    }

    public function send(string $id): JsonResponse
    {
        $id = InvoiceId::fromString($id);
        Event::dispatch(new InvoiceSendRequestEvent($id));

        return new JsonResponse('Invoice sending initiated.', Response::HTTP_ACCEPTED);
    }
}
