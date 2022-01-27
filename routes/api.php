<?php

//use Illuminate\Http\Request;
//use fase2\Events\TasksPusherEvent;
//use fase2\User;
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

Route::group(['middleware' => 'guest'], function(){
    Route::post('/users/login', 'UserController@apiLogin');
    Route::post('/users/register', 'UserController@apiRegister');

    Route::get('/queue', 'QueueController@index');
    
});

Route::group(['middleware' => 'auth:api'], function ($router) {//
    Route::get('/schedules', 'ScheduleController@getAll');
    Route::get('/schedules/{id}', 'ScheduleController@find');
    Route::post('/schedules', 'ScheduleController@add');
    Route::put('/schedules/{id}', 'ScheduleController@update');
    Route::delete('/schedules/{id}', 'ScheduleController@delete');

    Route::get('/clients', 'ClientController@getAll');
    Route::get('/clients/{id}', 'ClientController@find');
    Route::post('/clients', 'ClientController@add');
    Route::post('/clients/paginate', 'ClientController@getPaginate');
    Route::put('/clients/{id}', 'ClientController@update');
    Route::delete('/clients/{id}', 'ClientController@delete');

    Route::get('/providers', 'ProviderController@getAll');
    Route::get('/providers/{id}', 'ProviderController@find');
    Route::post('/providers', 'ProviderController@add');
    Route::put('/providers/{id}', 'ProviderController@update');
    Route::delete('/providers/{id}', 'ProviderController@delete');

    Route::get('/creditors', 'CreditorController@getAll');
    Route::get('/creditors/{id}', 'CreditorController@find');
    Route::post('/creditors', 'CreditorController@add');
    Route::put('/creditors/{id}', 'CreditorController@update');
    Route::delete('/creditors/{id}', 'CreditorController@delete');

    Route::get('/users', 'UserController@getAll');
    Route::get('/users/{id}', 'UserController@find');
    Route::post('/users/paginate', 'UserController@getPaginate');
    Route::post('/users/exist_user', 'UserController@existUsername');
    Route::post('/users', 'UserController@add');
    Route::put('/users/{id}', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@delete');

    Route::get('/agents', 'AgentController@getAll');
    Route::get('/agents/{id}', 'AgentController@find');
    Route::post('/agents/exist_user', 'AgentController@existUsername');
    Route::post('/agents', 'AgentController@add');
    Route::put('/agents/{id}', 'AgentController@update');
    Route::delete('/agents/{id}', 'AgentController@delete');

    Route::get('/roles', 'RolController@getAll');
    Route::get('/roles/{id}', 'RolController@find');
    Route::post('/roles', 'RolController@add');
    Route::put('/roles/{id}', 'RolController@update');
    Route::delete('/roles/{id}', 'RolController@delete');

    Route::get('/permissions', 'PermissionController@getAll');
    Route::get('/permissions/{id}', 'PermissionController@find');
    Route::post('/permissions', 'PermissionController@add');
    Route::put('/permissions/{id}', 'PermissionController@update');
    Route::delete('/permissions/{id}', 'PermissionController@delete');

    Route::get('/cat_references', 'CatReferenceController@getAll');
    Route::get('/cat_references/{id}', 'CatReferenceController@find');
    Route::post('/cat_references', 'CatReferenceController@add');
    Route::post('/cat_references/paginate', 'CatReferenceController@getPaginate');
    Route::put('/cat_references/{id}', 'CatReferenceController@update');
    Route::delete('/cat_references/{id}', 'CatReferenceController@delete');

    Route::get('/cat_packages', 'CatPackageController@getAll');
    Route::get('/cat_packages/{id}', 'CatPackageController@find');
    Route::post('/cat_packages', 'CatPackageController@add');
    Route::post('/cat_packages/paginate', 'CatPackageController@getPaginate');
    Route::put('/cat_packages/{id}', 'CatPackageController@update');
    Route::delete('/cat_packages/{id}', 'CatPackageController@delete');

    Route::get('/cat_products', 'CatProductController@getAll');
    Route::get('/cat_products/{id}', 'CatProductController@find');
    Route::post('/cat_products', 'CatProductController@add');
    Route::post('/cat_products/paginate', 'CatProductController@getPaginate');
    Route::put('/cat_products/{id}', 'CatProductController@update');
    Route::delete('/cat_products/{id}', 'CatProductController@delete');

    Route::get('/cat_pills', 'CatPillController@getAll');
    Route::get('/cat_pills/{id}', 'CatPillController@find');
    Route::post('/cat_pills', 'CatPillController@add');
    Route::post('/cat_pills/paginate', 'CatPillController@getPaginate');
    Route::put('/cat_pills/{id}', 'CatPillController@update');
    Route::delete('/cat_pills/{id}', 'CatPillController@delete');

    Route::get('/cat_services', 'CatServiceController@getAll');
    Route::get('/cat_services/{id}', 'CatServiceController@find');
    Route::post('/cat_services', 'CatServiceController@add');
    Route::post('/cat_services/paginate', 'CatServiceController@getPaginate');
    Route::put('/cat_services/{id}', 'CatServiceController@update');
    Route::delete('/cat_services/{id}', 'CatServiceController@delete');

    Route::get('/cat_type_sales', 'CatTypeSalesController@getAll');
    Route::get('/cat_type_sales/{id}', 'CatTypeSalesController@find');
    Route::post('/cat_type_sales', 'CatTypeSalesController@add');
    Route::post('/cat_type_sales/paginate', 'CatTypeSalesController@getPaginate');
    Route::put('/cat_type_sales/{id}', 'CatTypeSalesController@update');
    Route::delete('/cat_type_sales/{id}', 'CatTypeSalesController@delete');

    Route::get('/cat_expenses', 'CatExpensesController@getAll');
    Route::post('/cat_expenses', 'CatExpensesController@add');
    Route::post('/cat_expenses/paginate', 'CatExpensesController@getPaginate');
    Route::put('/cat_expenses/{id}', 'CatExpensesController@update');
    Route::delete('/cat_expenses/{id}', 'CatExpensesController@delete');

    Route::get('/cat_concepts', 'CatConceptController@getAll');
    Route::post('/cat_concepts', 'CatConceptController@add');
    Route::post('/cat_concepts/paginate', 'CatConceptController@getPaginate');
    Route::put('/cat_concepts/{id}', 'CatConceptController@update');
    Route::delete('/cat_concepts/{id}', 'CatConceptController@delete');

    Route::get('/sales', 'SaleController@getAll');
    Route::get('/sales/sales_day', 'SaleController@getForDay');
    Route::get('/sales/{id}', 'SaleController@findId');
    Route::get('/sales/user/{id}', 'SaleController@getSalesUserDay');
    
    Route::post('/sales', 'SaleController@add');
    Route::post('/sales/cute_now', 'SaleController@cuteSales');
    Route::post('/sales/cute_day', 'SaleController@getCuteSales');
    Route::post('/sales/paginate', 'SaleController@getPaginate');
    Route::post('/sales/user_day', 'SaleController@getSalesUserDay');
    Route::post('/sales/cancel/{id}', 'SaleController@cancel');
    Route::put('/sales/{id}', 'SaleController@update');
    Route::delete('/sales/{id}', 'SaleController@delete');

    Route::get('/sale_additionals/{id}', 'SaleAdditionalController@find');
    Route::post('/sale_additionals', 'SaleAdditionalController@add');
    Route::put('/sale_additionals/{id}', 'SaleAdditionalController@update');
    Route::delete('/sale_additionals/{id}', 'SaleAdditionalController@delete');

    Route::get('/products_inventory', 'ProductInventoryController@getAll');
    Route::get('/products_inventory/{id}', 'ProductInventoryController@find');
    Route::get('/products_inventory/product/{id}', 'ProductInventoryController@forProduct');
    Route::post('/products_inventory', 'ProductInventoryController@add');
    Route::put('/products_inventory/{id}', 'ProductInventoryController@update');
    Route::delete('/products_inventory/{id}', 'ProductInventoryController@delete');

    Route::get('/pills_inventory', 'PillInventoryController@getAll');
    Route::get('/pills_inventory/{id}', 'PillInventoryController@find');
    Route::get('/pills_inventory/pill/{id}', 'PillInventoryController@forPill');
    Route::post('/pills_inventory', 'PillInventoryController@add');
    Route::put('/pills_inventory/{id}', 'PillInventoryController@update');
    Route::delete('/pills_inventory/{id}', 'PillInventoryController@delete');

    Route::get('/payments', 'PaymentController@getAll');
    Route::get('/payments/{id}', 'PaymentController@find');
    Route::get('/payments/for_sale/{id}', 'PaymentController@forSaleId');
    Route::post('/payments', 'PaymentController@add');
    Route::put('/payments/{id}', 'PaymentController@update');
    Route::delete('/payments/{id}', 'PaymentController@delete');

    Route::get('/departments', 'DepartmentController@getAll');
    Route::get('/departments/{id}', 'DepartmentController@find');
    Route::get('/departments/{id}/sales', 'DepartmentController@getSales');
    Route::post('/departments', 'DepartmentController@add');
    Route::put('/departments/{id}', 'DepartmentController@update');
    Route::delete('/departments/{id}', 'DepartmentController@delete');

    Route::get('/packages', 'PackageController@getAll');
    Route::get('/packages/{id}', 'PackageController@find');
    Route::post('/packages', 'PackageController@add');
    Route::post('/packages/is_completed', 'PackageController@isCompleted');
    Route::post('/packages/paginate', 'PackageController@getPaginate');
    Route::put('/packages/{id}', 'PackageController@update');
    Route::delete('/packages/{id}', 'PackageController@delete');
    

    Route::get('/packages_tracking', 'PackageTrackingController@getAll');
    Route::get('/packages_tracking/{id}', 'PackageTrackingController@find');
    Route::get('/packages_tracking/for_package/{id}', 'PackageTrackingController@forPackageId');
    Route::post('/packages_tracking', 'PackageTrackingController@add');
    Route::put('/packages_tracking/{id}', 'PackageTrackingController@update');
    Route::delete('/packages_tracking/{id}', 'PackageTrackingController@delete');

    Route::get('/purchases', 'PurchaseController@getAll');
    Route::post('/purchases', 'PurchaseController@add');
    Route::post('/purchases/paginate', 'PurchaseController@getPaginate');
    Route::post('/purchases/pay/{id}', 'PurchaseController@pay');
    Route::post('/purchases/cancel/{id}', 'PurchaseController@cancel');
    Route::put('/purchases/{id}', 'PurchaseController@update');
    Route::delete('/purchases/{id}', 'PurchaseController@delete');

    Route::post('/box/balance', 'BoxController@getBalance');
    Route::post('/box/sales_chart', 'BoxController@getSalesChart');
    Route::post('/box/sales_package', 'BoxController@getSalesForPackageChart');
    Route::post('/box/sales_service', 'BoxController@getSalesForServiceChart');
    Route::post('/box/sales_service', 'BoxController@getSalesForServiceChart');
     
});