<?php

declare(strict_types=1);

namespace Tests\Feature\Notification\Http;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        $this->setUpFaker();

        parent::setUp();
    }

    #[DataProvider('hookActionProvider')]
    public function testHook(string $action): void
    {
        $invoice = InvoiceModel::create([
            'id' => Uuid::v7(),
            'customer_name' => 'Luke Skywalker',
            'customer_email' => 'luke@tatooine.xyz',
            'status' => StatusEnum::Sending,
            'total_price' => 0,
        ]);

        $uri = route('notification.hook', [
            'action' => $action,
            'reference' => $invoice->id,
        ]);

        $this->getJson($uri)->assertOk();
    }

    public function testInvalid(): void
    {
        $params = [
            'action' => 'dummy',
            'reference' => $this->faker->numberBetween(),
        ];

        $uri = route('notification.hook', $params);
        $this->getJson($uri)->assertNotFound();
    }

    public static function hookActionProvider(): array
    {
        return [
            ['delivered'],
            ['dummy'],
        ];
    }
}
