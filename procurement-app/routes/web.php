<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('items.index');
});

Route::resource('items', ItemController::class);

Route::resource('purchase-orders', PurchaseOrderController::class)
    ->parameters([
        'purchase-orders' => 'po',
    ]);

Route::post('purchase-orders/{po}/receive', [PurchaseOrderController::class, 'receive'])
    ->name('purchase-orders.receive');