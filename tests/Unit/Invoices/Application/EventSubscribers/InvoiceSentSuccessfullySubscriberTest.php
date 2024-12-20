<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\EventSubscribers\InvoiceCreationRequestSubscriber;
use Modules\Invoices\Application\EventSubscribers\InvoiceSentSuccessfullySubscriber;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Notifications\Application\Events\ResourceSentSuccessfullyEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class InvoiceSentSuccessfullySubscriberTest extends TestCase
{
    public function testHandleDispatchesCreateInvoiceCommand(): void
    {
        $event = new ResourceSentSuccessfullyEvent(Uuid::v7());

        Bus::fake();

        $subscriber = new InvoiceSentSuccessfullySubscriber();
        $subscriber->handle($event);

        Bus::assertDispatched(ChangeInvoiceStatusCommand::class);
    }
}
