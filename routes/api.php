<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\CreditorController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CatReferenceController;
use App\Http\Controllers\CatPackageController;
use App\Http\Controllers\CatProductController;
use App\Http\Controllers\CatServiceController;
use App\Http\Controllers\CatTypeSalesController;
use App\Http\Controllers\CatExpensesController;
use App\Http\Controllers\CatConceptController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleAdditionalController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageTrackingController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\BoxController;

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

Route::controller(UserController::class)->group(function(){
    Route::post('/auth/register', 'apiRegister');
    Route::post('/auth/login', 'apiLogin');
});

//Route::get('/queue', 'QueueController@index');

Route::group(['middleware' => 'jwt.verify'], function ($router) {//

    Route::controller(ScheduleController::class)->group(function () {
        Route::get('/schedules', 'getAll');
        Route::get('/schedules/{id}', 'find');
        Route::post('/schedules', 'add');
        Route::put('/schedules/{id}', 'update');
        Route::delete('/schedules/{id}', 'delete');
    });

    Route::controller(ClientController::class)->group(function () {
        Route::get('/clients', 'getAll');
        Route::get('/clients/{id}', 'find');
        Route::post('/clients', 'add');
        Route::post('/clients/paginate', 'getPaginate');
        Route::put('/clients/{id}', 'update');
        Route::delete('/clients/{id}', 'delete');
    });

    Route::controller(ProviderController::class)->group(function () {
        Route::get('/providers', 'getAll');
        Route::get('/providers/{id}', 'find');
        Route::post('/providers', 'add');
        Route::put('/providers/{id}', 'update');
        Route::delete('/providers/{id}', 'delete');
    });

    Route::controller(CreditorController::class)->group(function () {
        Route::get('/creditors', 'getAll');
        Route::get('/creditors/{id}', 'find');
        Route::post('/creditors', 'add');
        Route::put('/creditors/{id}', 'update');
        Route::delete('/creditors/{id}', 'delete');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'getAll');
        Route::get('/users/{id}', 'find');
        Route::post('/users/paginate', 'getPaginate');
        Route::post('/users/exist_user', 'existUsername');
        Route::post('/users', 'add');
        Route::put('/users/{id}', 'update');
        Route::delete('/users/{id}', 'delete');

        Route::post('/auth/logout', 'apiLogout');
    });

    Route::controller(AgentController::class)->group(function () {
        Route::get('/agents', 'getAll');
        Route::get('/agents/{id}', 'find');
        Route::post('/agents/exist_user', 'existUsername');
        Route::post('/agents', 'add');
        Route::put('/agents/{id}', 'update');
        Route::delete('/agents/{id}', 'delete');
    });

    Route::controller(CatReferenceController::class)->group(function () {
        Route::get('/cat_references', 'getAll');
        Route::get('/cat_references/{id}', 'find');
        Route::post('/cat_references', 'add');
        Route::post('/cat_references/paginate', 'getPaginate');
        Route::put('/cat_references/{id}', 'update');
        Route::delete('/cat_references/{id}', 'delete');
    });

    Route::controller(CatPackageController::class)->group(function () {
        Route::get('/cat_packages', 'getAll');
        Route::get('/cat_packages/{id}', 'find');
        Route::post('/cat_packages', 'add');
        Route::post('/cat_packages/paginate', 'getPaginate');
        Route::put('/cat_packages/{id}', 'update');
        Route::delete('/cat_packages/{id}', 'delete');
    });

    Route::controller(CatProductController::class)->group(function () {
        Route::get('/cat_products', 'getAll');
        Route::get('/cat_products/{id}', 'find');
        Route::post('/cat_products', 'add');
        Route::post('/cat_products/paginate', 'getPaginate');
        Route::put('/cat_products/{id}', 'update');
        Route::delete('/cat_products/{id}', 'delete');
    });

    Route::controller(CatServiceController::class)->group(function () {
        Route::get('/cat_services', 'getAll');
        Route::get('/cat_services/{id}', 'find');
        Route::post('/cat_services', 'add');
        Route::post('/cat_services/paginate', 'getPaginate');
        Route::put('/cat_services/{id}', 'update');
        Route::delete('/cat_services/{id}', 'delete');
    });

    Route::controller(CatTypeSalesController::class)->group(function () {
        Route::get('/cat_type_sales', 'getAll');
        Route::get('/cat_type_sales/{id}', 'find');
        Route::post('/cat_type_sales', 'add');
        Route::post('/cat_type_sales/paginate', 'getPaginate');
        Route::put('/cat_type_sales/{id}', 'update');
        Route::delete('/cat_type_sales/{id}', 'delete');
    });

    Route::controller(CatExpensesController::class)->group(function () {
        Route::get('/cat_expenses', 'getAll');
        Route::post('/cat_expenses', 'add');
        Route::post('/cat_expenses/paginate', 'getPaginate');
        Route::put('/cat_expenses/{id}', 'update');
        Route::delete('/cat_expenses/{id}', 'delete');
    });

    Route::controller(CatConceptController::class)->group(function () {
        Route::get('/cat_concepts', 'getAll');
        Route::post('/cat_concepts', 'add');
        Route::post('/cat_concepts/paginate', 'getPaginate');
        Route::put('/cat_concepts/{id}', 'update');
        Route::delete('/cat_concepts/{id}', 'delete');
    });

    Route::controller(SaleController::class)->group(function () {
        Route::get('/sales', 'getAll');
        Route::get('/sales/sales_day', 'getForDay');
        Route::get('/sales/{id}', 'findId');
        Route::get('/sales/user/{id}', 'getSalesUserDay');
        Route::post('/sales', 'add');
        Route::post('/sales/cute_now', 'cuteSales');
        Route::post('/sales/cute_day', 'getCuteSales');
        Route::post('/sales/paginate', 'getPaginate');
        Route::post('/sales/user_day', 'getSalesUserDay');
        Route::post('/sales/cancel/{id}', 'cancel');
        Route::put('/sales/{id}', 'update');
        Route::delete('/sales/{id}', 'delete');
    });

    Route::controller(SaleAdditionalController::class)->group(function () {
        Route::get('/sale_additionals/{id}', 'find');
        Route::post('/sale_additionals', 'add');
        Route::put('/sale_additionals/{id}', 'update');
        Route::delete('/sale_additionals/{id}', 'delete');
    });

    Route::controller(ProductInventoryController::class)->group(function () {
        Route::get('/products_inventory', 'getAll');
        Route::get('/products_inventory/{id}', 'find');
        Route::get('/products_inventory/product/{id}', 'forProduct');
        Route::post('/products_inventory', 'add');
        Route::put('/products_inventory/{id}', 'update');
        Route::delete('/products_inventory/{id}', 'delete');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::get('/payments', 'getAll');
        Route::get('/payments/{id}', 'find');
        Route::get('/payments/for_sale/{id}', 'forSaleId');
        Route::post('/payments', 'add');
        Route::put('/payments/{id}', 'update');
        Route::delete('/payments/{id}', 'delete');
    });

    Route::controller(DepartmentController::class)->group(function () {
        Route::get('/departments', 'getAll');
        Route::get('/departments/{id}', 'find');
        Route::get('/departments/{id}/sales', 'getSales');
        Route::post('/departments', 'add');
        Route::put('/departments/{id}', 'update');
        Route::delete('/departments/{id}', 'delete');
    });

    Route::controller(PackageController::class)->group(function () {
        Route::get('/packages', 'getAll');
        Route::get('/packages/{id}', 'find');
        Route::post('/packages', 'add');
        Route::post('/packages/is_completed', 'isCompleted');
        Route::post('/packages/paginate', 'getPaginate');
        Route::put('/packages/{id}', 'update');
        Route::delete('/packages/{id}', 'delete');
    });

    Route::controller(PackageTrackingController::class)->group(function () {
        Route::get('/packages_tracking', 'getAll');
        Route::get('/packages_tracking/{id}', 'find');
        Route::get('/packages_tracking/for_package/{id}', 'forPackageId');
        Route::post('/packages_tracking', 'add');
        Route::put('/packages_tracking/{id}', 'update');
        Route::delete('/packages_tracking/{id}', 'delete');
    });

    Route::controller(PurchaseController::class)->group(function () {
        Route::get('/purchases', 'getAll');
        Route::post('/purchases', 'add');
        Route::post('/purchases/paginate', 'getPaginate');
        Route::post('/purchases/pay/{id}', 'pay');
        Route::post('/purchases/cancel/{id}', 'cancel');
        Route::put('/purchases/{id}', 'update');
        Route::delete('/purchases/{id}', 'delete');
    });

    Route::controller(BoxController::class)->group(function () {
        Route::post('/box/balance', 'getBalance');
        Route::post('/box/sales_chart', 'getSalesChart');
        Route::post('/box/sales_package', 'getSalesForPackageChart');
        Route::post('/box/sales_service', 'getSalesForServiceChart');
        Route::post('/box/sales_service', 'getSalesForServiceChart');
    });

});

