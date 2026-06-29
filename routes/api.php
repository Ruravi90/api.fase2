<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\CatConceptController;
use App\Http\Controllers\CatExpensesController;
use App\Http\Controllers\CatPackageController;
use App\Http\Controllers\CatPillController;
use App\Http\Controllers\CatProductController;
use App\Http\Controllers\CatReferenceController;
use App\Http\Controllers\CatServiceController;
use App\Http\Controllers\CatTypeSalesController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageTrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PillInventoryController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SaleAdditionalController;
use App\Domains\Sales\Controllers\SaleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClinicalNoteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});
Route::get('/saas/available-plans', [\App\Http\Controllers\Saas\PlanController::class, 'index']);

Route::post('/users/login', [UserController::class, 'apiLogin']);

Route::middleware('guest')->group(function () {
    Route::post('/users/register', [UserController::class, 'apiRegister']);
    Route::post('/saas/register', [\App\Http\Controllers\Saas\TenantController::class, 'publicRegister']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/logout', [UserController::class, 'apiLogout']);
    Route::get('/users/me', [UserController::class, 'apiMe']);
    // Rutas para Clínicas (Tenants) gestionando su suscripción SaaS
    Route::post('/saas/payment/preference', [\App\Http\Controllers\Saas\MercadoPagoController::class, 'createPreference']);
    Route::post('/saas/promo-codes/validate', [\App\Http\Controllers\Saas\PromoCodeController::class, 'validateCode']);

    Route::middleware('check_subscription')->group(function () {
        Route::get('/init/schedule', [\App\Http\Controllers\InitController::class, 'getScheduleInit']);
        Route::get('/init/sale', [\App\Http\Controllers\InitController::class, 'getSaleInit']);
        Route::get('/init/dashboard', [\App\Http\Controllers\InitController::class, 'getDashboardInit']);

        Route::get('/queue/active', [QueueController::class, 'getActiveQueue']);
        Route::post('/queue/advance', [QueueController::class, 'advanceTurn']);
        Route::controller(ScheduleController::class)->group(function () {
            Route::get('/schedules', 'getAll');
            Route::get('/schedules/{id}', 'find');
            Route::post('/schedules', 'add');
            Route::post('/schedules/{id}/check-in', 'checkIn');
            Route::put('/schedules/{id}', 'update');
            Route::delete('/schedules/{id}', 'delete');
        });

        Route::controller(ClinicalNoteController::class)->group(function () {
            Route::get('/clinical_notes/history/{clientId}', 'getHistory');
            Route::post('/clinical_notes/draft', 'saveDraft');
            Route::post('/clinical_notes/{id}/sign', 'signNote');
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
    });

    Route::controller(AgentController::class)->group(function () {
        Route::get('/agents', 'getAll');
        Route::get('/agents/{id}', 'find');
        Route::post('/agents/exist_user', 'existUsername');
        Route::post('/agents', 'add');
        Route::put('/agents/{id}', 'update');
        Route::delete('/agents/{id}', 'delete');
    });

    Route::controller(RolController::class)->group(function () {
        Route::get('/roles', 'getAll');
        Route::get('/roles/{id}', 'find');
        Route::post('/roles', 'add');
        Route::put('/roles/{id}', 'update');
        Route::delete('/roles/{id}', 'delete');
    });

    Route::controller(PermissionController::class)->group(function () {
        Route::get('/permissions', 'getAll');
        Route::get('/permissions/{id}', 'find');
        Route::post('/permissions', 'add');
        Route::put('/permissions/{id}', 'update');
        Route::delete('/permissions/{id}', 'delete');
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

    Route::controller(CatPillController::class)->group(function () {
        Route::get('/cat_pills', 'getAll');
        Route::get('/cat_pills/{id}', 'find');
        Route::post('/cat_pills', 'add');
        Route::post('/cat_pills/paginate', 'getPaginate');
        Route::put('/cat_pills/{id}', 'update');
        Route::delete('/cat_pills/{id}', 'delete');
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

    Route::controller(PillInventoryController::class)->group(function () {
        Route::get('/pills_inventory', 'getAll');
        Route::get('/pills_inventory/{id}', 'find');
        Route::get('/pills_inventory/pill/{id}', 'forPill');
        Route::post('/pills_inventory', 'add');
        Route::put('/pills_inventory/{id}', 'update');
        Route::delete('/pills_inventory/{id}', 'delete');
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
        Route::get('/clients/{id}/active-packages', 'activeForClient');
        Route::get('/packages/completed', 'isCompleted');
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
        Route::post('/box/dashboard_summary', 'getDashboardSummary');
        Route::post('/box/sales_department', 'getSalesDepartmentChart');
        Route::post('/box/payment_methods', 'getPaymentMethodsChart');
        Route::post('/box/top_sellers', 'getTopSellers');
        Route::get('/box/recent_activity', 'getRecentActivity');
        Route::get('/box/alerts', 'getDashboardAlerts');
    });

    Route::controller(\App\Http\Controllers\OpenwaController::class)->group(function () {
        Route::get('/openwa/sessions', 'index');
        Route::post('/openwa/sessions', 'store');
        Route::post('/openwa/sessions/{id}/start', 'start');
        Route::get('/openwa/sessions/{id}/qr', 'getQr');
        Route::delete('/openwa/sessions/{id}', 'destroy');
    });

        Route::controller(\App\Http\Controllers\ChatController::class)->group(function () {
            Route::get('/chat/conversations', 'index');
            Route::get('/chat/conversations/{id}/messages', 'show');
            Route::post('/chat/conversations/{id}/messages', 'store');
        });
    }); // End check_subscription
});

Route::post('/saas/payment/webhook', [\App\Http\Controllers\Saas\MercadoPagoController::class, 'webhook']);

// ---------------------------------------------------------
// RUTAS DEL DUEÑO DEL SAAS (Super Admin)
// ---------------------------------------------------------
Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('saas')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Saas\DashboardController::class, 'index']);
    
    Route::apiResource('tenants', \App\Http\Controllers\Saas\TenantController::class);
    Route::post('tenants/{tenant}/assign-plan', [\App\Http\Controllers\Saas\TenantController::class, 'assignPlan']);
    Route::apiResource('plans', \App\Http\Controllers\Saas\PlanController::class);
    Route::apiResource('subscriptions', \App\Http\Controllers\Saas\SubscriptionController::class)->only(['index', 'show']);
    Route::apiResource('promo-codes', \App\Http\Controllers\Saas\AdminPromoCodeController::class);
});
