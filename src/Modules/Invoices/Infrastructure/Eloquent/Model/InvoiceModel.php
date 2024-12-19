<?php
declare (strict_types=1);

namespace Modules\Invoices\Infrastructure\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;

class InvoiceModel extends Model
{
    protected $table = 'invoices';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_name',
        'customer_email',
        'status',
        'total_price',
    ];

    protected $casts = [
        'total_price' => 'integer',
        'status' => StatusEnum::class,
    ];

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceProductLineModel::class, 'invoice_id', 'id');
    }
}
