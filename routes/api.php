<?php

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\TestingResultController;
use App\Http\Controllers\Api\BatchApi;
use App\Http\Controllers\Api\CropControllerApi;
use App\Http\Controllers\Api\DiseaseControllerApi;
use App\Http\Controllers\Api\FarmControllerApi;
use App\Http\Controllers\Api\IngredientApi;
use App\Http\Controllers\Api\KeyApi;
use App\Http\Controllers\Api\LoginControllerApi;
use App\Http\Controllers\Api\PlantingAreaApi;
use App\Http\Controllers\Api\PlotControllerApi;
use App\Http\Controllers\Api\SeedGardenControllerApi;
use App\Http\Controllers\Api\TypeOfPusApi;
use App\Http\Controllers\Api\UnitApi;
use App\Http\Controllers\Api\WarehouseControllerApi;
use App\Http\Controllers\Api\WorkControllerApi;
use App\Http\Controllers\Api\WorkerControllerApi;
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
    Route::get('all-plots', [PlotControllerApi::class, 'index']);
    Route::get('detail-plots/{id}', [PlotControllerApi::class, 'detail']);

    Route::get('all-seedgardens', [SeedGardenControllerApi::class, 'index']);
    Route::get('detail-seedgardens/{id}', [SeedGardenControllerApi::class, 'detail']);

    Route::get('all-plants', [CropControllerApi::class, 'index']);
    Route::get('detail-plants/{id}', [CropControllerApi::class, 'detail']);

    Route::get('all-dutys', [WorkerControllerApi::class, 'dutys']);
    Route::get('all-teams', [WorkerControllerApi::class, 'teams']);
    Route::get('all-workers', [WorkerControllerApi::class, 'workers']);
    Route::get('detail-workers/{id}', [WorkerControllerApi::class, 'detail']);

    Route::get('all-works', [WorkControllerApi::class, 'typeWorks']);
    Route::get('all-gentasks', [WorkControllerApi::class, 'gentasks']);
    Route::get('detail-gentasks/{id}', [WorkControllerApi::class, 'detailG']);
    Route::get('all-workps', [WorkControllerApi::class, 'workps']);
    Route::get('detail-workps/{id}', [WorkControllerApi::class, 'detailW']);
    Route::get('all-evaluate', [WorkControllerApi::class, 'evaluate']);

    Route::get('all-diseaseplants', [DiseaseControllerApi::class, 'diseasePlants']);
    Route::get('detail-diseaseplants/{id}', [DiseaseControllerApi::class, 'detailD']);
    Route::get('all-treatmentslips', [DiseaseControllerApi::class, 'treatmentslips']);
    Route::get('detail-treatmentslips/{id}', [DiseaseControllerApi::class, 'detailT']);
    Route::get('all-materialproposals', [DiseaseControllerApi::class, 'materialproposals']);
    Route::get('detail-materialproposals/{id}', [DiseaseControllerApi::class, 'detailM']);
    Route::get('all-fallentplants', [DiseaseControllerApi::class, 'fallentplants']);

    Route::get('all-warehouses', [WarehouseControllerApi::class, 'warehouses']);
    Route::get('all-categories', [WarehouseControllerApi::class, 'categories']);
    Route::get('all-units', [WarehouseControllerApi::class, 'units']);
    Route::get('all-pwarehouses', [WarehouseControllerApi::class, 'pwarehouses']);
    Route::get('detail-detailPw/{id}', [WarehouseControllerApi::class, 'detailPw']);
    Route::get('all-decomposes', [WarehouseControllerApi::class, 'decomposes']);
    Route::get('detail-decomposes/{id}', [WarehouseControllerApi::class, 'detailD']);
    Route::get('all-outputs', [WarehouseControllerApi::class, 'outputs']);
    Route::get('detail-outputs/{id}', [WarehouseControllerApi::class, 'detailO']);
    Route::get('all-inventorystocks', [WarehouseControllerApi::class, 'inventorystocks']);

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
