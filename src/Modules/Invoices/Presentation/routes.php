<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoiceController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('id', (new GenericValidator)->getPattern());

Route::group(['prefix' => 'invoices'], function () {
    Route::get('/{id}', InvoiceController::class . '@show');
    Route::post('/', InvoiceController::class . '@store');
    Route::post('/send/{id}', InvoiceController::class . '@send');
});
