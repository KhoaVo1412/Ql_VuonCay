<?php

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\TestingResultController;
use App\Http\Controllers\Api\BatchApi;
use App\Http\Controllers\Api\FarmControllerApi;
use App\Http\Controllers\Api\IngredientApi;
use App\Http\Controllers\Api\KeyApi;
use App\Http\Controllers\Api\LoginControllerApi;
use App\Http\Controllers\Api\PlantingAreaApi;
use App\Http\Controllers\Api\TypeOfPusApi;
use App\Http\Controllers\Api\UnitApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginControllerApi::class, 'login']);
Route::post('/login-api', [LoginControllerApi::class, 'login_api']);
Route::post('/change-password', [LoginControllerApi::class, 'changePassword']);
Route::post('/logout-api', [LoginControllerApi::class, 'logout_api']);
Route::post('/reset-pass-api', [LoginControllerApi::class, 'sendResetLinkEmail']);
// Route::get('/export-batch', [BatchApi::class, 'index'])->name('batches.indexapi');
Route::get('/check-auth', function () {
    return response()->json(Auth::user());
});
Route::middleware('apitoken')->group(function () {
    Route::get('all-key', [KeyApi::class, 'index']);
    Route::get('webmap', [KeyApi::class, 'webmap']);
    Route::get('appmap', [KeyApi::class, 'appmap']);
    Route::get('all-farm', [FarmControllerApi::class, 'index']);
    Route::get('all-unit', [UnitApi::class, 'index']);
    Route::get('all-typeofpus', [TypeOfPusApi::class, 'index']);
    Route::get('all-vehicle', [TypeOfPusApi::class, 'index_vhc']);
    Route::get('all-ingredients', [IngredientApi::class, 'index']);
    Route::get('details-ingredients/{id}', [IngredientApi::class, 'detail']);
    Route::get('all-plantingareas', [PlantingAreaApi::class, 'index']);
    Route::get('details-plantingareas/{id}', [PlantingAreaApi::class, 'detail']);
    Route::get('/export-batch', [BatchApi::class, 'index'])->name('batches.indexapi');
    Route::get('/contracts/list', [ContractController::class, 'getList']);
    Route::get('/contracts/list1', [ContractController::class, 'getList1']);
    Route::get('all-customer', [ContractController::class, 'index_customer']);

    Route::get('/contracts/types', [ContractController::class, 'getContractType']);
    Route::get('/contracts/get/{id}', [ContractController::class, 'getDetail']);

    Route::get('/testing/{id}', [TestingResultController::class, 'testing']);

    Route::get('/certificates/list', [CertificateController::class, 'getCertificates']);

    Route::get('/contracts/get-detail-order/{id}', [ContractController::class, 'getDetailOrder']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
