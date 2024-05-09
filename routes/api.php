<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(ImportController::class)->group(function () {
    Route::get('truncate', [ImportController::class, 'truncateTables']);
    Route::get('migrate', [ImportController::class, 'index']);
    Route::get('insert_approved_service_order_itens', [ImportController::class, 'callSPInsertApprovedServiceOrderItens']);
    Route::get('insert_approved_service_orders', [ImportController::class, 'callSPInsertApprovedServiceOrders']);
    Route::get('insert_accredited_suppliers', [ImportController::class, 'callSPInsertAccreditedSuppliers']);
    Route::get('create_approved_orders', [ImportController::class, 'callSPCreateApprovedOrders']);


});
