<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BatchBController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\TestingResultController;
use App\Models\Plant;

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CheckActionController;
use App\Http\Controllers\Admin\CheckLoginController;
use App\Http\Controllers\Admin\FarmController;
use App\Http\Controllers\Admin\InfIngredientController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlantingAreaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TypeOfPusController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\OutputController;
use App\Http\Controllers\Admin\WarehousesController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\DutyController;
use App\Http\Controllers\Admin\WorkerEvaluationController;
use App\Http\Controllers\OrderBatchController;

use App\Http\Controllers\Export\ExportExcelController;
use App\Http\Controllers\Export\ExportLatexController;
use App\Http\Controllers\Export\ExportRssController;
use App\Http\Controllers\Export\ExportSvrController;
use App\Http\Controllers\Import\PlantingAreaImportController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\Import\ImportLatexController;
use App\Http\Controllers\Import\ImportRssController;
use App\Http\Controllers\Import\ImportSvrController;
use App\Http\Controllers\Admin\ContractFileController;
use App\Http\Controllers\Admin\CropController;
use App\Http\Controllers\Admin\DiseaseplanController;
use App\Http\Controllers\Import\ImportBatchIngControllor;
use App\Http\Controllers\Import\ImportIngredientControllor;

use App\Http\Controllers\Admin\DueDiliStateController;
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\GeojsonController;
use App\Http\Controllers\Admin\MaterialProposalController;
use App\Http\Controllers\Admin\PlotController;
use App\Http\Controllers\Admin\SeedGardenController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TreatmentSlipController;
use App\Http\Controllers\Admin\TreesFelledController;
use App\Http\Controllers\Admin\ListMaterialController;
use App\Http\Controllers\Admin\UnitsController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\Admin\PWareHouseController;
use App\Http\Controllers\Admin\DecomposeController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\WorkProposalsController;
use App\Http\Controllers\Import\UpdatePlantingAreaImportController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

// Route::get('/create', [LoginController::class, 'createuser']);
Route::post('/select-redirect', [LoginController::class, 'selectRedirect'])->name('select.redirect');


Route::middleware(['login'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    // Route::get('/tx', [HomeController::class, 'index'])->name('tx');
    // Route::get('/api/contracts', [HomeController::class, 'fetchContracts']);
    // Route::get('/api/contracts/detail/{id}', [HomeController::class, 'getContractDetails']);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/account', [UserController::class, 'index'])->name('account');
    Route::post('/account', [UserController::class, 'store']);
    Route::get('/farm-vehicle-statistics', [ChartController::class, 'getFarmAndVehicleStatistics']);

    Route::put('/account/update', [UserController::class, 'update'])->name('account.update');


    Route::resources([
        'contract-types' => ContractTypeController::class,
        'customers' => CustomerController::class,
        'contracts' => ContractController::class,
        'testing' => TestingResultController::class,
        'certi' => CertificateController::class,

    ]);

    Route::get('show-untested/{id}', [TestingResultController::class, 'showun'])->name('showun');


    Route::get('/get-certificates/data', [CertificateController::class, 'getCertificatesData'])->name('certificates.data');
    Route::get('/contract-get-data', [ContractTypeController::class, 'getData'])->name('contract.types.data');
    Route::get('/customer-get-data', [CustomerController::class, 'getData']);
    Route::get('/contracts-get-data', [ContractController::class, 'getData']);
    Route::get('/testing-get-data', [TestingResultController::class, 'getData']);

    Route::get('/untested', [TestingResultController::class, 'indexUntested'])->name('untested');
    Route::get('/untested-get-data', [TestingResultController::class, 'getDataUntested']);

    Route::delete('/delete-order', [ContractController::class, 'delete_order'])->name('order.delete');

    Route::get('/farms', [FarmController::class, 'index'])->name('farms.index');
    Route::post('/save-farms', [FarmController::class, 'save'])->name('farms.save');
    Route::get('/edit-farms/{id}', [FarmController::class, 'edit'])->name('farms.edit');
    Route::post('/farms/update/{id}', [FarmController::class, 'update'])->name('farms.update');
    Route::get('farms/delete/{id}', [FarmController::class, 'destroy'])->name('farms.delete');
    Route::post('/farms/edit-multiple', [FarmController::class, 'editMultiple'])->name('farms.editMultiple');
    Route::post('/farms/delete-multiple', [FarmController::class, 'deleteMultiple'])->name('farms.deleteMultiple');
    Route::post('/toggle-farm-status', [FarmController::class, 'toggleStatus'])->name('farm.status');

    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::post('/save-teams', [TeamController::class, 'save'])->name('teams.save');
    Route::get('/edit-teams/{id}', [TeamController::class, 'edit'])->name('teams.edit');
    Route::post('/teams/update/{id}', [TeamController::class, 'update'])->name('teams.update');
    Route::get('teams/delete/{id}', [TeamController::class, 'destroy'])->name('teams.delete');
    Route::post('/teams/edit-multiple', [TeamController::class, 'editMultiple'])->name('teams.editMultiple');
    Route::post('/teams/delete-multiple', [TeamController::class, 'deleteMultiple'])->name('teams.deleteMultiple');
    Route::post('/toggle-teams-status', [TeamController::class, 'toggleStatus'])->name('teams.status');

    Route::get('/aworks', [WorkController::class, 'index1'])->name('aworks.index');
    Route::post('/save-aworks', [WorkController::class, 'save1'])->name('aworks.save');
    Route::get('/edit-aworks/{id}', [WorkController::class, 'edit1'])->name('aworks.edit');
    Route::post('/aworks/update/{id}', [WorkController::class, 'update1'])->name('aworks.update');
    Route::get('aworks/delete/{id}', [WorkController::class, 'destroy1'])->name('aworks.delete');
    Route::post('/aworks/edit-multiple', [WorkController::class, 'editMultiple1'])->name('aworks.editMultiple');
    Route::post('/aworks/delete-multiple', [WorkController::class, 'deleteMultiple1'])->name('aworks.deleteMultiple');
    Route::post('/toggle-aworks-status', [WorkController::class, 'toggleStatus1'])->name('aworks.status');

    Route::get('/duty', [DutyController::class, 'index'])->name('duty.index');
    Route::post('/save-duty', [DutyController::class, 'save'])->name('duty.save');
    Route::get('/edit-duty/{id}', [DutyController::class, 'edit'])->name('duty.edit');
    Route::post('/duty/update/{id}', [DutyController::class, 'update'])->name('duty.update');
    Route::get('duty/delete/{id}', [DutyController::class, 'destroy'])->name('duty.delete');
    Route::post('/duty/edit-multiple', [DutyController::class, 'editMultiple'])->name('duty.editMultiple');
    Route::post('/duty/delete-multiple', [DutyController::class, 'deleteMultiple'])->name('duty.deleteMultiple');
    Route::post('/toggle-duty-status', [DutyController::class, 'toggleStatus'])->name('duty.status');

    Route::get('/plots', [PlotController::class, 'index'])->name('plots.index');
    Route::get('/add-plots', [PlotController::class, 'add'])->name('plots.add');
    Route::post('/save-plots', [PlotController::class, 'save'])->name('plots.save');
    Route::get('/edit-plots/{id}', [PlotController::class, 'edit'])->name('plots.edit');
    Route::post('/plots/update/{id}', [PlotController::class, 'update'])->name('plots.update');
    Route::get('plots/delete/{id}', [PlotController::class, 'destroy'])->name('plots.delete');
    Route::post('/plots/edit-multiple', [PlotController::class, 'editMultiple'])->name('plots.editMultiple');
    Route::post('/plots/delete-multiple', [PlotController::class, 'deleteMultiple'])->name('plots.deleteMultiple');
    Route::post('/toggle-plot-status', [PlotController::class, 'toggleStatus'])->name('plots.status');

    Route::get('/crops', [CropController::class, 'index'])->name('crops.index');
    Route::post('/save-crops', [CropController::class, 'save'])->name('crops.save');
    Route::get('/edit-crops/{id}', [CropController::class, 'edit'])->name('crops.edit');
    Route::post('/crops/update/{id}', [CropController::class, 'update'])->name('crops.update');
    Route::get('crops/delete/{id}', [CropController::class, 'destroy'])->name('crops.delete');
    Route::post('/crops/edit-multiple', [CropController::class, 'editMultiple'])->name('crops.editMultiple');
    Route::post('/crops/delete-multiple', [CropController::class, 'deleteMultiple'])->name('crops.deleteMultiple');
    Route::post('/toggle-crop-status', [CropController::class, 'toggleStatus'])->name('crops.status');

    Route::get('/works', [WorkController::class, 'index'])->name('works.index');
    Route::get('/add-works', [WorkController::class, 'add'])->name('works.add');
    Route::post('/save-works', [WorkController::class, 'save'])->name('works.save');
    Route::get('/edit-works/{id}', [WorkController::class, 'edit'])->name('works.edit');
    Route::post('/works/update/{id}', [WorkController::class, 'update'])->name('works.update');
    Route::get('works/delete/{id}', [WorkController::class, 'destroy'])->name('works.delete');
    Route::post('/works/edit-multiple', [WorkController::class, 'editMultiple'])->name('works.editMultiple');
    Route::post('/works/delete-multiple', [WorkController::class, 'deleteMultiple'])->name('works.deleteMultiple');
    Route::post('/toggle-works-status', [WorkController::class, 'toggleStatus'])->name('works.status');

    Route::get('/plots/{plotID}/plants', [WorkController::class, 'getPlantsByPlot']);
    Route::get('/get-plants-for-plot/{plotID}', function ($plotID) {
        $plants = Plant::where('plotID', $plotID)->get();
        return response()->json($plants);
    });

    Route::get('/api/products-by-category/{categoryID}', function ($categoryID) {
        // $products = App\Models\Product::where('categoryID', $categoryID)->get(['id', 'name']);
        $products = App\Models\Product::where('categoryID', $categoryID)->get();
        return response()->json($products);
    });

    Route::get('/product/{id}/unit', [PWareHouseController::class, 'getUnit']);

    Route::get('/workevs', [WorkerEvaluationController::class, 'index'])->name('workevs.index');
    Route::post('/save-workevs', [WorkerEvaluationController::class, 'save'])->name('workevs.save');
    Route::get('/edit-workevs/{id}', [WorkerEvaluationController::class, 'edit'])->name('workevs.edit');
    Route::post('/workevs/update/{id}', [WorkerEvaluationController::class, 'update'])->name('workevs.update');
    Route::get('workevs/delete/{id}', [WorkerEvaluationController::class, 'destroy'])->name('workevs.delete');
    Route::post('/workevs/edit-multiple', [WorkerEvaluationController::class, 'editMultiple'])->name('workevs.editMultiple');
    Route::post('/workevs/delete-multiple', [WorkerEvaluationController::class, 'deleteMultiple'])->name('workevs.deleteMultiple');
    Route::post('/toggle-workevs-status', [WorkerEvaluationController::class, 'toggleStatus'])->name('workevs.status');

    Route::get('/decomposes', [DecomposeController::class, 'index'])->name('decomposes.index');
    Route::get('/add-decomposes', [DecomposeController::class, 'add'])->name('decomposes.add');
    Route::post('/save-decomposes', [DecomposeController::class, 'save'])->name('decomposes.save');
    Route::get('/edit-decomposes/{id}', [DecomposeController::class, 'edit'])->name('decomposes.edit');
    Route::post('/decomposes/update/{id}', [DecomposeController::class, 'update'])->name('decomposes.update');
    Route::get('decomposes/delete/{id}', [DecomposeController::class, 'destroy'])->name('decomposes.delete');
    Route::post('/decomposes/edit-multiple', [DecomposeController::class, 'editMultiple'])->name('decomposes.editMultiple');
    Route::post('/decomposes/delete-multiple', [DecomposeController::class, 'deleteMultiple'])->name('decomposes.deleteMultiple');
    Route::post('/toggle-decomposes-status', [DecomposeController::class, 'toggleStatus'])->name('decomposes.status');

    Route::get('/pwarehouses', [PWareHouseController::class, 'index'])->name('pwarehouses.index');
    Route::get('/add-pwarehouses', [PWareHouseController::class, 'add'])->name('pwarehouses.add');
    Route::post('/save-pwarehouses', [PWareHouseController::class, 'save'])->name('pwarehouses.save');
    Route::get('/edit-pwarehouses/{id}', [PWareHouseController::class, 'edit'])->name('pwarehouses.edit');
    Route::post('/pwarehouses/update/{id}', [PWareHouseController::class, 'update'])->name('pwarehouses.update');
    Route::get('pwarehouses/delete/{id}', [PWareHouseController::class, 'destroy'])->name('pwarehouses.delete');
    Route::post('/pwarehouses/edit-multiple', [PWareHouseController::class, 'editMultiple'])->name('pwarehouses.editMultiple');
    Route::post('/pwarehouses/delete-multiple', [PWareHouseController::class, 'deleteMultiple'])->name('pwarehouses.deleteMultiple');
    Route::post('/toggle-pwarehouses-status', [PWareHouseController::class, 'toggleStatus'])->name('pwarehouses.status');

    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/save-comments', [CommentController::class, 'save'])->name('comments.save');
    Route::get('/edit-comments/{id}', [CommentController::class, 'edit'])->name('comments.edit');
    Route::post('/comments/update/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::get('comments/delete/{id}', [CommentController::class, 'destroy'])->name('comments.delete');
    Route::post('/comments/edit-multiple', [CommentController::class, 'editMultiple'])->name('comments.editMultiple');
    Route::post('/comments/delete-multiple', [CommentController::class, 'deleteMultiple'])->name('comments.deleteMultiple');
    Route::post('/toggle-comments-status', [CommentController::class, 'toggleStatus'])->name('comments.status');

    Route::get('/outputs', [OutputController::class, 'index'])->name('outputs.index');
    Route::post('/save-outputs', [OutputController::class, 'save'])->name('outputs.save');
    Route::get('/edit-outputs/{id}', [OutputController::class, 'edit'])->name('outputs.edit');
    Route::post('/outputs/update/{id}', [OutputController::class, 'update'])->name('outputs.update');
    Route::get('outputs/delete/{id}', [OutputController::class, 'destroy'])->name('outputs.delete');
    Route::post('/outputs/edit-multiple', [OutputController::class, 'editMultiple'])->name('outputs.editMultiple');
    Route::post('/outputs/delete-multiple', [OutputController::class, 'deleteMultiple'])->name('outputs.deleteMultiple');
    Route::post('/toggle-outputs-status', [OutputController::class, 'toggleStatus'])->name('outputs.status');

    Route::get('/workers', [WorkerController::class, 'index'])->name('workers.index');
    Route::get('/add-workers', [WorkerController::class, 'add'])->name('workers.add');
    Route::post('/save-workers', [WorkerController::class, 'save'])->name('workers.save');
    Route::get('/edit-workers/{id}', [WorkerController::class, 'edit'])->name('workers.edit');
    Route::post('/workers/update/{id}', [WorkerController::class, 'update'])->name('workers.update');
    Route::get('workers/delete/{id}', [WorkerController::class, 'destroy'])->name('workers.delete');
    Route::post('/workers/edit-multiple', [WorkerController::class, 'editMultiple'])->name('workers.editMultiple');
    Route::post('/workers/delete-multiple', [WorkerController::class, 'deleteMultiple'])->name('workers.deleteMultiple');
    Route::post('/toggle-worker-status', [WorkerController::class, 'toggleStatus'])->name('workers.status');

    Route::get('/diseaseplans', [DiseaseplanController::class, 'index'])->name('diseaseplans.index');
    Route::get('/add-diseaseplans', [DiseaseplanController::class, 'add'])->name('diseaseplans.add');
    Route::post('/save-diseaseplans', [DiseaseplanController::class, 'save'])->name('diseaseplans.save');
    Route::get('/edit-diseaseplans/{id}', [DiseaseplanController::class, 'edit'])->name('diseaseplans.edit');
    Route::post('/diseaseplans/update/{id}', [DiseaseplanController::class, 'update'])->name('diseaseplans.update');
    Route::get('diseaseplans/delete/{id}', [DiseaseplanController::class, 'destroy'])->name('diseaseplans.delete');
    Route::post('/diseaseplans/edit-multiple', [DiseaseplanController::class, 'editMultiple'])->name('diseaseplans.editMultiple');
    Route::post('/diseaseplans/delete-multiple', [DiseaseplanController::class, 'deleteMultiple'])->name('diseaseplans.deleteMultiple');
    Route::post('/toggle-diseaseplan-status', [DiseaseplanController::class, 'toggleStatus'])->name('diseaseplan.status');

    Route::get('/materialproposals', [MaterialProposalController::class, 'index'])->name('materialproposals.index');
    Route::get('/add-materialproposals', [MaterialProposalController::class, 'add'])->name('materialproposals.add');
    Route::post('/save-materialproposals', [MaterialProposalController::class, 'save'])->name('materialproposals.save');
    Route::get('/materialproposals/edit/{id}', [MaterialProposalController::class, 'edit'])->name('materialproposals.edit');
    Route::post('/materialproposals/update/{id}', [MaterialProposalController::class, 'update'])->name('materialproposals.update');
    Route::get('materialproposals/delete/{id}', [MaterialProposalController::class, 'destroy'])->name('materialproposals.delete');
    Route::post('/materialproposals/edit-multiple', [MaterialProposalController::class, 'editMultiple'])->name('materialproposals.editMultiple');
    Route::post('/materialproposals/delete-multiple', [MaterialProposalController::class, 'deleteMultiple'])->name('materialproposals.deleteMultiple');
    Route::post('/toggle-materialproposal-status', [MaterialProposalController::class, 'toggleStatus'])->name('materialproposal.status');

    Route::get('/treatmentslips', [TreatmentSlipController::class, 'index'])->name('treatmentslips.index');
    Route::get('/add-treatmentslips', [TreatmentSlipController::class, 'add'])->name('treatmentslips.add');
    Route::post('/save-treatmentslips', [TreatmentSlipController::class, 'save'])->name('treatmentslips.save');
    Route::get('/treatmentslips/edit/{id}', [TreatmentSlipController::class, 'edit'])->name('treatmentslips.edit');
    Route::post('/treatmentslips/update/{id}', [TreatmentSlipController::class, 'update'])->name('treatmentslips.update');
    Route::get('treatmentslips/delete/{id}', [TreatmentSlipController::class, 'destroy'])->name('treatmentslips.delete');
    Route::post('/treatmentslips/edit-multiple', [TreatmentSlipController::class, 'editMultiple'])->name('treatmentslips.editMultiple');
    Route::post('/treatmentslips/delete-multiple', [TreatmentSlipController::class, 'deleteMultiple'])->name('treatmentslips.deleteMultiple');
    Route::post('/toggle-treatmentslip-status', [TreatmentSlipController::class, 'toggleStatus'])->name('treatmentslip.status');

    Route::get('/treesfelleds', [TreesFelledController::class, 'index'])->name('treesfelleds.index');
    Route::post('/save-treesfelleds', [TreesFelledController::class, 'save'])->name('treesfelleds.save');
    Route::get('/edittreesfelleds/{id}', [TreesFelledController::class, 'edit'])->name('treesfelleds.edit');
    Route::post('/treesfelleds/update/{id}', [TreesFelledController::class, 'update'])->name('treesfelleds.update');
    Route::get('treesfelleds/delete/{id}', [TreesFelledController::class, 'destroy'])->name('treesfelleds.delete');
    Route::post('/treesfelleds/edit-multiple', [TreesFelledController::class, 'editMultiple'])->name('treesfelleds.editMultiple');
    Route::post('/treesfelleds/delete-multiple', [TreesFelledController::class, 'deleteMultiple'])->name('treesfelleds.deleteMultiple');
    Route::post('/toggle-treesfelleds-status', [TreesFelledController::class, 'toggleStatus'])->name('treesfelleds.status');

    Route::get('/units', [UnitsController::class, 'index'])->name('units.index');
    Route::post('/save-units', [UnitsController::class, 'save'])->name('units.save');
    Route::get('/edit-units/{id}', [UnitsController::class, 'edit'])->name('units.edit');
    Route::post('/units/update/{id}', [UnitsController::class, 'update'])->name('units.update');
    Route::get('units/delete/{id}', [UnitsController::class, 'destroy'])->name('units.delete');
    Route::post('/units/edit-multiple', [UnitsController::class, 'editMultiple'])->name('units.editMultiple');
    Route::post('/units/delete-multiple', [UnitsController::class, 'deleteMultiple'])->name('units.deleteMultiple');
    Route::post('/toggle-units-status', [UnitsController::class, 'toggleStatus'])->name('units.status');

    Route::get('/products', [ListMaterialController::class, 'index'])->name('products.index');
    Route::post('/save-products', [ListMaterialController::class, 'save'])->name('products.save');
    Route::get('/edit-products/{id}', [ListMaterialController::class, 'edit'])->name('products.edit');
    Route::post('/products/update/{id}', [ListMaterialController::class, 'update'])->name('products.update');
    Route::get('products/delete/{id}', [ListMaterialController::class, 'destroy'])->name('products.delete');
    Route::post('/products/edit-multiple', [ListMaterialController::class, 'editMultiple'])->name('products.editMultiple');
    Route::post('/products/delete-multiple', [ListMaterialController::class, 'deleteMultiple'])->name('products.deleteMultiple');
    Route::post('/toggle-products-status', [ListMaterialController::class, 'toggleStatus'])->name('products.status');

    Route::get('/seedgardens', [SeedGardenController::class, 'index'])->name('seedgardens.index');
    Route::post('/save-seedgardens', [SeedGardenController::class, 'save'])->name('seedgardens.save');
    Route::get('/edit-seedgardens/{id}', [SeedGardenController::class, 'edit'])->name('seedgardens.edit');
    Route::post('/seedgardens/update/{id}', [SeedGardenController::class, 'update'])->name('seedgardens.update');
    Route::get('seedgardens/delete/{id}', [SeedGardenController::class, 'destroy'])->name('seedgardens.delete');
    Route::post('/seedgardens/edit-multiple', [SeedGardenController::class, 'editMultiple'])->name('seedgardens.editMultiple');
    Route::post('/seedgardens/delete-multiple', [SeedGardenController::class, 'deleteMultiple'])->name('seedgardens.deleteMultiple');
    Route::post('/toggle-seedgardens-status', [SeedGardenController::class, 'toggleStatus'])->name('seedgardens.status');
    Route::post('/seedgardens/confirm-delete-multiple', [SeedGardenController::class, 'confirmDeleteMultiple'])->name('seedgardens.confirm-delete-multiple');
    Route::post('/seedgardens/update-status', [SeedGardenController::class, 'updateStatus']);

    Route::get('/warehouses', [WarehousesController::class, 'index'])->name('warehouses.index');
    Route::post('/save-warehouses', [WarehousesController::class, 'save'])->name('warehouses.save');
    Route::get('/edit-warehouses/{id}', [WarehousesController::class, 'edit'])->name('warehouses.edit');
    Route::post('/warehouses/update/{id}', [WarehousesController::class, 'update'])->name('warehouses.update');
    Route::get('warehouses/delete/{id}', [WarehousesController::class, 'destroy'])->name('warehouses.delete');
    Route::post('/warehouses/edit-multiple', [WarehousesController::class, 'editMultiple'])->name('warehouses.editMultiple');
    Route::post('/warehouses/delete-multiple', [WarehousesController::class, 'deleteMultiple'])->name('warehouses.deleteMultiple');
    Route::post('/toggle-warehouses-status', [WarehousesController::class, 'toggleStatus'])->name('warehouses.status');
    Route::post('/warehouses/confirm-delete-multiple', [WarehousesController::class, 'confirmDeleteMultiple'])->name('warehouses.confirm-delete-multiple');
    Route::post('/warehouses/update-status', [WarehousesController::class, 'updateStatus']);

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/save-categories', [CategoryController::class, 'save'])->name('categories.save');
    Route::get('/edit-categories/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::get('categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');
    Route::post('/categories/edit-multiple', [CategoryController::class, 'editMultiple'])->name('categories.editMultiple');
    Route::post('/categories/delete-multiple', [CategoryController::class, 'deleteMultiple'])->name('categories.deleteMultiple');
    Route::post('/toggle-categories-status', [CategoryController::class, 'toggleStatus'])->name('categories.status');
    Route::post('/categories/confirm-delete-multiple', [CategoryController::class, 'confirmDeleteMultiple'])->name('categories.confirm-delete-multiple');
    Route::post('/categories/update-status', [CategoryController::class, 'updateStatus'])->name('categories.status');

    Route::get('/workps', [WorkProposalsController::class, 'index'])->name('workps.index');
    Route::get('/add-workps', [WorkProposalsController::class, 'add'])->name('workps.add');
    Route::post('/save-workps', [WorkProposalsController::class, 'save'])->name('workps.save');
    Route::get('/edit-workps/{id}', [WorkProposalsController::class, 'edit'])->name('workps.edit');
    Route::post('/workps/update/{id}', [WorkProposalsController::class, 'update'])->name('workps.update');
    Route::get('workps/delete/{id}', [WorkProposalsController::class, 'destroy'])->name('workps.delete');
    Route::post('/workps/edit-multiple', [WorkProposalsController::class, 'editMultiple'])->name('workps.editMultiple');
    Route::post('/workps/delete-multiple', [WorkProposalsController::class, 'deleteMultiple'])->name('workps.deleteMultiple');
    Route::post('/toggle-workps-status', [WorkProposalsController::class, 'toggleStatus'])->name('workps.status');

    // Route::get('/get-farms-by-unit', function (Request $request) {
    //     $unit = $request->query('unit');
    //     $farms = \App\Models\Farm::where('unit', $unit)->get(['id', 'farm_name']);
    //     return response()->json($farms);
    // });

    // Route::get('/api/get-farms-by-unit', function (Request $request) {
    //     $unit = $request->query('unit');
    //     $farms = \App\Models\Farm::where('unit', $unit)->where('status', 'Hoạt động')->get(['id', 'farm_name']);
    //     return response()->json($farms);
    // });


    Route::get('/typeofpus', [TypeOfPusController::class, 'index'])->name('typeofpus.index');
    Route::post('/save-typeofpus', [TypeOfPusController::class, 'save'])->name('typeofpus.save');
    Route::get('/typeofpus/edit/{id}', [TypeOfPusController::class, 'edit'])->name('typeofpus.edit');
    Route::post('/typeofpus/update/{id}', [TypeOfPusController::class, 'update'])->name('typeofpus.update');
    Route::get('typeofpus/delete/{id}', [TypeOfPusController::class, 'destroy'])->name('typeofpus.delete');
    Route::post('/typeofpus/edit-multiple', [TypeOfPusController::class, 'editMultiple'])->name('typeofpus.editMultiple');
    Route::post('/typeofpus/delete-multiple', [TypeOfPusController::class, 'deleteMultiple'])->name('typeofpus.deleteMultiple');
    Route::post('/toggle-typeofpus-status', [TypeOfPusController::class, 'toggleStatus'])->name('typeofpus.status');

    Route::get('/factorys', [FactoryController::class, 'index'])->name('factorys.index');
    Route::post('/save-factorys', [FactoryController::class, 'save'])->name('factorys.save');
    Route::get('/factorys/edit/{id}', [FactoryController::class, 'edit'])->name('factorys.edit');
    Route::post('/factorys/update/{id}', [FactoryController::class, 'update'])->name('factorys.update');
    Route::get('factorys/delete/{id}', [FactoryController::class, 'destroy'])->name('factorys.delete');
    Route::post('/factorys/edit-multiple', [FactoryController::class, 'editMultiple'])->name('factorys.editMultiple');
    Route::post('/factorys/delete-multiple', [FactoryController::class, 'deleteMultiple'])->name('factorys.deleteMultiple');
    Route::post('/toggle-factorys-status', [FactoryController::class, 'toggleStatus'])->name('factorys.status');

    Route::get('/plantingareas', [PlantingAreaController::class, 'index'])->name('plantingareas.index');
    Route::get('/add-plantingareas', [PlantingAreaController::class, 'add'])->name('add-plantingareas');
    Route::post('/save-plantingareas', [PlantingAreaController::class, 'save'])->name('save-plantingareas');
    Route::get('/edit-plantingareas/{id}', [PlantingAreaController::class, 'edit'])->name('edit-plantingareas');
    Route::post('/update-plantingareas/{id}', [PlantingAreaController::class, 'update'])->name('update-plantingareas');
    Route::get('/delete-plantingareas/{id}', [PlantingAreaController::class, 'destroy'])->name('delete-plantingareas');
    Route::post('/plantingareas/delete-multiple', [PlantingAreaController::class, 'deleteMultiple'])->name('plantingareas.deleteMultiple');
    Route::get('/get-farms-by-unit/{unitId}', [PlantingAreaController::class, 'getFarmsByUnit']);

    Route::get('/ingredients', [InfIngredientController::class, 'index'])->name('ingredients.index');
    Route::get('/add-ingredients', [InfIngredientController::class, 'add'])->name('add-ingredients');
    Route::post('/save-ingredients', [InfIngredientController::class, 'save'])->name('save-ingredients');
    Route::get('/edit-ingredients/{id}', [InfIngredientController::class, 'edit'])->name('edit-ingredients');
    Route::post('/update-ingredients/{id}', [InfIngredientController::class, 'update'])->name('update-ingredients');
    Route::get('/delete-ingredients/{id}', [InfIngredientController::class, 'destroy'])->name('delete-ingredients');
    Route::post('/ingredients/delete-multiple', [InfIngredientController::class, 'deleteMultiple'])->name('ingredients.deleteMultiple');
    Route::get('/get-vehicles', [InfIngredientController::class, 'getVehicles'])->name('get-vehicles');
    Route::get('/get-farms-by-unit', [InfIngredientController::class, 'getFarmsByUnit'])->name('get-farms-by-unit');
    Route::get('/get-chi-tieu', [InfIngredientController::class, 'getChiTieu'])->name('get.chi.tieu');
    Route::get('/select-chi-tieu', [InfIngredientController::class, 'selectChiTieu'])->name('select.chi.tieu');
    Route::get('/get-planting-areas-by-farm', [InfIngredientController::class, 'getPlantingAreasByFarm'])->name('get-planting-areas-by-farm');

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/add-batches', [BatchController::class, 'add'])->name('add-batches');
    Route::post('/save-batches', [BatchController::class, 'save'])->name('save-batches');
    Route::get('/edit-batches/{id}', [BatchController::class, 'edit'])->name('edit-batches');
    Route::post('/update-batches/{id}', [BatchController::class, 'update'])->name('update-batches');
    Route::get('/delete-batches/{id}', [BatchController::class, 'destroy'])->name('delete-batches');
    Route::post('/batch/delete-multiple', [BatchController::class, 'deleteMultiple'])->name('batch.deleteMultiple');

    Route::get('/batchesB', [BatchBController::class, 'index_b'])->name('batchesB.index');
    Route::get('/add-batchesB', [BatchBController::class, 'add_b'])->name('add-batchesB');
    Route::post('/save-batchesB', [BatchBController::class, 'save_b'])->name('save-batchesB');
    Route::get('/edit-batchesB/{id}', [BatchBController::class, 'edit_b'])->name('edit-batchesB');
    Route::post('/update-batchesB/{id}', [BatchBController::class, 'update_b'])->name('update-batchesB');
    Route::get('/delete-batchesB/{id}', [BatchBController::class, 'destroy_b'])->name('delete-batchesB');
    Route::post('/batchB/delete-multiple', [BatchBController::class, 'deleteMultiple'])->name('batchB.deleteMultiple');

    Route::get('/qrCode', [BatchBController::class, 'index_qr'])->name('index_qr.index');
    Route::post('/generate-qr', [BatchBController::class, 'generateQrCodes'])->name('generate_qr');


    Route::get('/get-batch', [OrderBatchController::class, 'getBatch'])->name('get.batch');

    Route::get('/orderbatch', [OrderBatchController::class, 'index'])->name('orderbatchs.index');
    Route::get('/add-orderbatchs', [OrderBatchController::class, 'add'])->name('add-orderbatchs');
    Route::post('/save-orderbatchs', [OrderBatchController::class, 'save'])->name('save-orderbatchs');
    Route::get('/edit-orderbatchs/{id}', [OrderBatchController::class, 'edit'])->name('edit-orderbatchs');
    Route::post('/update-orderbatchs', [OrderBatchController::class, 'update'])->name('update-orderbatchs');
    Route::get('/delete-orderbatchs/{id}', [OrderBatchController::class, 'destroy'])->name('delete-orderbatchs');
    Route::post('/orderbatchs/delete-multiple', [OrderBatchController::class, 'deleteMultiple'])->name('orderbatchs.deleteMultiple');

    Route::get('/export-contracts', [ContractController::class, 'export'])->name('export.contracts');

    Route::get('/cont', [ContractController::class, 'index'])->name('cont');
    Route::post('/save-contract', [ContractController::class, 'storeContract'])->name('contstore');
    Route::post('/cont-update/{id}', [ContractController::class, 'updateContract']);
    Route::post('/contracts/delete-multiple', [ContractController::class, 'deleteMultiple'])->name('contracts.deleteMultiple');

    Route::post('/customers/delete-multiple', [CustomerController::class, 'deleteMultiple'])->name('customers.deleteMultiple');
    Route::post('/contracttype/delete-multiple', [ContractTypeController::class, 'deleteMultiple'])->name('contracttype.deleteMultiple');


    Route::get('/check-login', [CheckLoginController::class, 'index'])->name('checkLogin.index');
    Route::get('/check-action', [CheckActionController::class, 'index'])->name('checkAction.index');

    // Import Excel SVR, LATEX, RSS
    Route::get('/file', [TestingResultController::class, 'importFiles'])->name('import.files');
    Route::post('/import-rss', [ImportRssController::class, 'importRss'])->name('import-rss');
    Route::post('/import-svr', [ImportSvrController::class, 'importSvr'])->name('import-svr');
    Route::post('/import-latex', [ImportLatexController::class, 'importLatex'])->name('import-latex');

    Route::get('/import-ing', [InfIngredientController::class, 'index_ip'])->name('importIng.index');
    Route::post('/import-file', [InfIngredientController::class, 'import'])->name('importIng.import');
    Route::get('/download-sample', [InfIngredientController::class, 'downloadSample'])->name('download.sample');

    Route::get('/import-batchIng', [ImportBatchIngControllor::class, 'index'])->name('importBatchIng.index');
    Route::post('/storeBatchIng', [ImportBatchIngControllor::class, 'importExcel'])->name('storeBatchIng');




    // Xuất Excel
    Route::get('/export-excel', [ExportExcelController::class, 'exportExcel']);

    // Thêm khu vực trồng bằng excel
    Route::get('/add-excel', [PlantingAreaImportController::class, 'add_excel'])->name('add-excel');
    Route::post('/import-plantingareas', [PlantingAreaImportController::class, 'importExcel'])->name('import-plantingareas');
    // Sửa khu vực trồng bằng excel
    Route::get('/edit-excel', [UpdatePlantingAreaImportController::class, 'edit_excel'])->name('edit-excel');
    Route::post('/edit-import-plantingareas', [UpdatePlantingAreaImportController::class, 'importExcel'])->name('edit-import-plantingareas');

    // Báo cáo
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    // Thêm file vô hợp đồng
    Route::get('/contract-files', [ContractFileController::class, 'index'])->name('contract-files.index');
    Route::get('/contract-files/data', [ContractFileController::class, 'getData'])->name('contract-files.data');

    Route::get('/contract-files/edit/{id}', [ContractFileController::class, 'edit'])->name('edit.index');
    Route::put('/contract-files/edit/{id}', [ContractFileController::class, 'editFile'])->name('contract-files.edit');

    Route::get('/contract-files/create-file', [ContractFileController::class, 'create'])->name('create-file.index');
    Route::post('/contract-files/create-file', [ContractFileController::class, 'createFile'])->name('create-file.createfile');

    // Web tra cứu geojson
    Route::get('/batch-geojson', [GeojsonController::class, 'index'])->name('geojson.index');
    Route::get('/batch-geojson/download-geojson', [GeojsonController::class, 'downloadGeojson'])->name('download.geojson');


    // Cài đặt
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/webmap', [SettingController::class, 'updateWm'])->name('settings.wm');
    Route::post('/settings/token', [SettingController::class, 'updateToken'])->name('settings.token');
    Route::post('/settings/update-login', [SettingController::class, 'updateLogin'])->name('settings.updateLogin');
    Route::post('/settings/update-AM', [SettingController::class, 'updateAppMap'])->name('settings.updateWebmap');
    Route::post('/settings/update-runtime', [SettingController::class, 'updateruntime'])->name('settings.updateruntime');
    Route::post('/settings/all-save', [SettingController::class, 'saveAllSettings'])->name('settings.saveAllSettings');
    // Xuất file excel theo lọc (Quản lý khu vực trồng)
    Route::get('/export-ingredients', [InfIngredientController::class, 'exportExcel'])->name('ingredients.export');
});
Route::get('/api/map', [HomeController::class, 'map']);

Route::middleware(['role.khachhang', 'lang'])->group(function () {
    Route::get('/truy-xuat', [HomeController::class, 'index'])->name('tx');
    Route::get('/api/contracts', [HomeController::class, 'fetchContracts']);
    Route::get('/api/contracts/detail/{id}', [HomeController::class, 'getContractDetails']);
    Route::get('/dds-export/{code}', [HomeController::class, 'ddsExportExcel'])->name('dds.export');
    Route::get('/change-language/{lang}', function ($lang) {

        if (in_array($lang, ["en", "vi", "de", "zh"])) {
            Session::put('locale', $lang);
            Session::save();
        }
        return redirect()->back();
    })->name('change-language');
});
Route::middleware(['lang'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
});
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'handle_login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::post('/check-email', [UserController::class, 'checkEmail']);
Route::group(['middleware' => ['isAdmin']], function () {
    Route::get('all-permissions', [PermissionController::class, 'all_permissions'])->name('all.permissions');
    Route::post('add-permissions', [PermissionController::class, 'store'])->name('store.permissions');
    Route::get('edit-permissions/{permission}', [PermissionController::class, 'show'])->name('show.permissions');
    Route::post('edit-permissions/{permission}', [PermissionController::class, 'update'])->name('edit.permissions');
    Route::delete('delete-permissions/{permission}', [PermissionController::class, 'delete'])->name('delete.permissions');

    Route::get('all-roles', [RoleController::class, 'all_roles'])->name('all.roles');
    Route::post('add-roles', [RoleController::class, 'store'])->name('store.roles');
    Route::get('edit-roles/{role}', [RoleController::class, 'show'])->name('show.roles');
    Route::post('edit-roles/{role}', [RoleController::class, 'update'])->name('edit.roles');
    Route::delete('delete-roles/{role}', [RoleController::class, 'delete'])->name('delete.roles');

    Route::get('give-permission/{role}', [RoleController::class, 'addPermissionToRole'])->name('show.per');
    Route::post('give-permission/{role}', [RoleController::class, 'postPermissionToRole']);

    Route::get('all-users', [UserController::class, 'all_users'])->name('all.users');
    Route::post('add-users', [UserController::class, 'store'])->name('store.users');
    Route::get('edit-users/{users}', [UserController::class, 'show'])->name('show.users');
    Route::post('edit-users/{users}', [UserController::class, 'update'])->name('edit.users');
    Route::delete('delete-users/{users}', [UserController::class, 'delete'])->name('delete.users');
    Route::get('filter-selects', [UserController::class, 'all_users'])->name('filter.users');

    Route::middleware(['permission:Xem Nông Trường'])->get('/farms', [FarmController::class, 'index'])->name('farms.index');
    Route::middleware(['permission:Thêm Nông Trường'])->post('/save-farms', [FarmController::class, 'save'])->name('farms.save');
    // Route::middleware(['permission:Sửa Nông Trường'])->get('edit-farms/{id}', [FarmController::class, 'edit'])->name('farms.edit');
    Route::middleware(['permission:Xóa Nông Trường'])->post('/farms/delete-multiple', [FarmController::class, 'deleteMultiple'])->name('farms.deleteMultiple');

    Route::middleware(['permission:Xem Xe'])->get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::middleware(['permission:Thêm Xe'])->post('/save-vehicles', [VehicleController::class, 'save'])->name('vehicles.save');
    Route::middleware(['permission:Sửa Xe'])->get('/vehicles/edit/{id}', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::middleware(['permission:Xóa Xe'])->post('/vehicles/delete-multiple', [VehicleController::class, 'deleteMultiple'])->name('vehicles.deleteMultiple');

    Route::middleware(['permission:Xem Nguyên Liệu'])->get('/ingredients', [InfIngredientController::class, 'index'])->name('ingredients.index');
    Route::middleware(['permission:Thêm Nguyên Liệu'])->get('/add-ingredients', [InfIngredientController::class, 'add'])->name('add-ingredients');
    Route::middleware(['permission:Sửa Nguyên Liệu'])->post('/update-ingredients/{id}', [InfIngredientController::class, 'update'])->name('update-ingredients');
    Route::middleware(['permission:Xóa Nguyên Liệu'])->post('/ingredients/delete-multiple', [InfIngredientController::class, 'deleteMultiple'])->name('ingredients.deleteMultiple');

    Route::middleware(['permission:Xem Khu Vực Trồng'])->get('/plantingareas', [PlantingAreaController::class, 'index'])->name('plantingareas.index');
    Route::middleware(['permission:Thêm Khu Vực Trồng'])->post('/add-plantingareas', [PlantingAreaController::class, 'save'])->name('add-plantingareas');
    Route::middleware(['permission:Sửa Khu Vực Trồng'])->post('/update-plantingareas/{id}', [PlantingAreaController::class, 'update'])->name('update-plantingareas');
    Route::middleware(['permission:Xóa Khu Vực Trồng'])->post('/plantingareas/delete-multiple', [PlantingAreaController::class, 'deleteMultiple'])->name('deletedeleteMultiple');

    Route::middleware(['permission:Xem Mã Lô'])->get('/batchesB', [BatchBController::class, 'index_b'])->name('batchesB.index');
    Route::middleware(['permission:Thêm Mã Lô'])->get('/add-batchesB', [BatchBController::class, 'add_b'])->name('add-batchesB');
    Route::middleware(['permission:Sửa Mã Lô'])->get('/edit-batchesB/{id}', [BatchBController::class, 'edit_b'])->name('edit-batchesB');
    Route::middleware(['permission:Xóa Mã Lô'])->post('/batchB/delete-multiple', [BatchBController::class, 'deleteMultiple'])->name('batchB.deleteMultiple');

    Route::middleware(['permission:Xem Lô Hàng'])->get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::middleware(['permission:Thêm Lô Hàng'])->get('/add-batches', [BatchController::class, 'add'])->name('add-batches');
    Route::middleware(['permission:Sửa Lô Hàng'])->post('/update-batches/{id}', [BatchController::class, 'update'])->name('update-batches');
    Route::middleware(['permission:Xóa Lô Hàng'])->post('/batch/delete-multiple', [BatchController::class, 'deleteMultiple'])->name('batch.deleteMultiple');

    // MDF qlcl
    Route::middleware(['permission:Xem Quản Lý Chất Lượng'])->get('/untested', [TestingResultController::class, 'indexUntested'])->name('untested');
    Route::middleware(['permission:Xem Quản Lý Chất Lượng'])->resource('testing', TestingResultController::class, [
        'only' => ['index'],
    ]);
    Route::middleware(['permission:Thêm Quản Lý Chất Lượng'])->resource('testing', TestingResultController::class, [
        'only' => ['create', 'store'],
    ]);
    Route::middleware(['permission:Sửa Quản Lý Chất Lượng'])->get('show-untested/{id}', [TestingResultController::class, 'showun'])->name('showun');
    Route::middleware(['permission:Xóa Quản Lý Chất Lượng'])->resource('testing', TestingResultController::class, [
        'only' => ['destroy'],
    ]);
    // MDW Loại hợp đồng
    Route::middleware(['permission:Xem Loại Hợp Đồng'])->resource('contract-types', ContractTypeController::class, [
        'only' => ['index'],
    ]);
    Route::middleware(['permission:Thêm Loại Hợp Đồng'])->resource('contract-types', ContractTypeController::class, [
        'only' => ['create', 'store'],
    ]);
    Route::middleware(['permission:Sửa Loại Hợp Đồng'])->resource('contract-types', ContractTypeController::class, [
        'only' => ['edit', 'update'],
    ]);
    Route::middleware(['permission:Xóa Loại Hợp Đồng'])->resource('contract-types', ContractTypeController::class, [
        'only' => ['destroy'],
    ]);

    //Hop dong
    Route::middleware(['permission:Xem Hợp Đồng'])->get('/cont', [ContractController::class, 'index'])->name('cont');

    Route::middleware(['permission:Thêm Hợp Đồng'])->resource('contracts', ContractController::class, [
        'only' => ['create'],
    ]);
    Route::middleware(['permission:Sửa Hợp Đồng'])->resource('contracts', ContractController::class, [
        'only' => ['edit'],
    ]);

    Route::get('/contract-dueDiligenceStatement', [DueDiliStateController::class, 'index'])->name('duedilistate.index');
    Route::get('/contract-dueDiligenceStatement/{id}', [DueDiliStateController::class, 'exportExcel'])->name('duedilistate.export');

    //midw tài khoản
    Route::middleware(['permission:Xem Tài Khoản'])->get('/all-users', [UserController::class, 'all_users'])->name('all.users');
    Route::middleware(['permission:Thêm Tài Khoản'])->post('/add-users', [UserController::class, 'store'])->name('store.users');
    Route::middleware(['permission:Sửa Tài Khoản'])->get('/edit-users/{users}', [UserController::class, 'show'])->name('show.users');
    Route::middleware(['permission:Xóa Tài Khoản'])->delete('/delete-users/{users}', [UserController::class, 'delete'])->name('delete.users');
});


Route::get('/password/forgot', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/forgot', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'resetPassword'])->name('password.update');
Route::get('/forgot-password', [LoginController::class, 'forgotIndex'])->name('forgotIndex');

Route::get('/contract/dds/export/{code}', [ContractController::class, 'ddsExportExcel'])->name('export.dds.order');
Route::get('/contract/dds2/export/{code}', [ContractController::class, 'dds2ExportExcel'])->name('export.dds2.order');
Route::get('/contract/dds3/export/{code}', [ContractController::class, 'dds3ExportExcel'])->name('export.dds3.order');
Route::get('export/dds/order/{order_code}/batch/{batch_id}', [ContractController::class, 'getBatchDDS'])->name('export.dds.order.batch');
Route::delete('/delete-item', [CheckLoginController::class, 'destroy']);

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    // Artisan::call('permission:cache-reset');

    return "Cache cleared successfully!";
});
