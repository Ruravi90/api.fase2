<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\CatPackage;
use App\Models\CatService;
use App\Models\CatTypeSale;
use App\Models\Department;
use App\Models\User;
use App\Models\Sale;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class InitController extends Controller
{
    public function getScheduleInit()
    {
        $clients = Client::all();
        $packages = CatPackage::all();
        $services = CatService::all();
        $typeSales = CatTypeSale::all();
        $departments = Department::all();
        
        $roles = Role::where('name', 'agent')->with('users')->first();
        $agents = $roles ? $roles->users : collect();

        return response()->json([
            'clients' => $clients,
            'cat_packages' => $packages,
            'cat_services' => $services,
            'cat_type_sales' => $typeSales,
            'departments' => $departments,
            'agents' => $agents
        ]);
    }

    public function getSaleInit(Request $request)
    {
        $departments = Department::all();
        $typeSales = CatTypeSale::all();
        
        $roles = Role::where('name', 'agent')->with('users')->first();
        $agents = $roles ? $roles->users : collect();

        // Sales for the day
        $userId = $request->query('user_id') ?? auth()->id();
        $from = Carbon::today()->toDateTimeString();
        $sales = Sale::with([
            'department', 'client', 'responsible', 'type', 'user',
            'sales', 'sales.cat_package', 'sales.cat_service',
            'sales.cat_pill', 'sales.type', 'sales.cat_product',
            'sales.payments' => function ($query) use ($from) {
                $query->where('created_at', '>=', $from);
            }
        ])
        ->where('primary_id', null)
        ->where('updated_at', '>=', $from)
        ->where('is_cute', 0)
        ->where('user_id', $userId)
        ->orderBy('id', 'desc')
        ->get();

        return response()->json([
            'departments' => $departments,
            'cat_type_sales' => $typeSales,
            'agents' => $agents,
            'sales_day' => $sales
        ]);
    }

    public function getDashboardInit(Request $request)
    {
        $boxController = new BoxController();
        $productInventory = ProductInventory::with('product')->get();
        $pillInventory = PillInventory::with('pill')->get();

        return response()->json([
            'summary' => json_decode($boxController->getDashboardSummary($request)->getContent()),
            'sales' => json_decode($boxController->getSalesChart($request)->getContent()),
            'packages' => json_decode($boxController->getSalesForPackageChart($request)->getContent()),
            'services' => json_decode($boxController->getSalesForServiceChart($request)->getContent()),
            'departments' => json_decode($boxController->getSalesDepartmentChart($request)->getContent()),
            'paymentMethods' => json_decode($boxController->getPaymentMethodsChart($request)->getContent()),
            'topSellers' => json_decode($boxController->getTopSellers($request)->getContent()),
            'activity' => json_decode($boxController->getRecentActivity($request)->getContent()),
            'alerts' => json_decode($boxController->getDashboardAlerts($request)->getContent()),
            'productsInv' => $productInventory,
            'pillsInv' => $pillInventory
        ]);
    }
}
