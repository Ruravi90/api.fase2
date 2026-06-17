<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Department;
use App\Models\CatExpense;
use App\Models\HistoryBalance;
use App\Models\Package as SpaPackage;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class BoxController extends Controller
{
	public function index(){
		return view('box.index');
	}

	public function getBalance(Request $request){
		$dt = new DateTime();
		if($request->has("date")){
			$dt = new DateTime($request->get("date"));
		}
		$start = Carbon::instance($dt)->startOfMonth();
		$end = Carbon::instance($dt)->endOfMonth();

		$departaments = Department::all();
		$json = array();
		$count = 0;

		foreach ($departaments as $_departmanet){ 
			$catExpenses = CatExpense::all();

			$json[$count]['dateStart'] = $start->toDateTimeString();
			$json[$count]['dateEnd'] = $end->toDateTimeString();

			$json[$count]['name'] = $_departmanet->name;
			
			$subCount = 0;
			$expenses = array();
			foreach ($catExpenses as $catExpense){ 
				$expenses[$subCount]['name'] = $catExpense->name;

				$expenseTotal = Purchase::where('department_id',$_departmanet->id)
				->where('expence_id',$catExpense->id)
				->where('is_paid',1)
				->where('updated_at','>=', $start->toDateTimeString())
				->where('updated_at','<=', $end->toDateTimeString())
				->sum('amount');

				$expenses[$subCount]['total']  = intval($expenseTotal);
				
				$subCount++;
			}
			
			$json[$count]['expenses'] = $expenses;

			$purchaseTotal = Purchase::where('department_id',$_departmanet->id)
			->where('is_paid',1)
			->where('updated_at','>=', $start->toDateTimeString())
			->where('updated_at','<=', $end->toDateTimeString())
			->sum('amount');

			$salesTotal = Sale::where('department_id',$_departmanet->id)
			->where('is_paid',1)
			->where('primary_id','<>', null)
			->where('updated_at','>=', $start->toDateTimeString())
			->where('updated_at','<=', $end->toDateTimeString())
			->sum('amount');

			$history = HistoryBalance::where('updated_at','<',$end->toDateTimeString())
			->where('department_id',$_departmanet->id)
			->orderBy('id', 'DESC')
			->first();

			if($history != null && intval($history->utility) > 0){
				$salesTotal = (intval($salesTotal) + intval($history->utility));
			}

			$json[$count]['purchaseTotal'] = intval($purchaseTotal);
			$json[$count]['salesTotal'] = intval($salesTotal);
			$json[$count]['total'] = ($salesTotal - $purchaseTotal);

			$count++;
		}
		
		return response()->json($json);
	}
	
	public function getSalesChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::where('primary_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));
			
				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));
			
				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('date')->get();

    	return response()->json($json);
	}
	
	public function getSalesForPackageChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::with('cat_package')
		->where('primary_id','<>', null)
		->where('package_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales,package_id'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));
				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales,package_id'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));
				
				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('package_id','date')->get();
		
    	return response()->json($json);
	}
	
	public function getSalesForServiceChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::with('cat_service')
		->where('primary_id','<>', null)
		->where('service_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales,service_id'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));
				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales,service_id'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));
				
				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('service_id','date')->get();
		
    	return response()->json($json);
    }

	public function getDashboardSummary(Request $request){
		$todayStart = Carbon::now()->startOfDay();
		$todayEnd = Carbon::now()->endOfDay();
		$monthStart = Carbon::now()->startOfMonth();
		$monthEnd = Carbon::now()->endOfMonth();

		$paidSales = Sale::where('primary_id','<>', null)
		->where('is_cancel', 0)
		->where('is_paid', 1);

		$pendingSales = Sale::where('primary_id','<>', null)
		->where('is_cancel', 0)
		->where('is_paid', 0);

		return response()->json([
			'salesToday' => (clone $paidSales)->whereBetween('updated_at', [$todayStart, $todayEnd])->count(),
			'salesMonth' => (clone $paidSales)->whereBetween('updated_at', [$monthStart, $monthEnd])->count(),
			'revenueToday' => intval((clone $paidSales)->whereBetween('updated_at', [$todayStart, $todayEnd])->sum('amount')),
			'revenueMonth' => intval((clone $paidSales)->whereBetween('updated_at', [$monthStart, $monthEnd])->sum('amount')),
			'pendingPayments' => (clone $pendingSales)->count(),
			'pendingAmount' => intval((clone $pendingSales)->sum('balance')),
			'activePackages' => SpaPackage::where('is_completed', 0)->count(),
			'completedPackages' => SpaPackage::where('is_completed', 1)->count(),
			'lowStockProducts' => ProductInventory::where('count', '<=', 5)->count(),
			'lowStockPills' => PillInventory::where('count', '<=', 5)->count(),
		]);
	}

	public function getSalesDepartmentChart(Request $request){
		$json = Sale::with('department')
		->where('primary_id','<>', null)
		->where('is_cancel', 0)
		->select(DB::raw('count(id) as sales'), DB::raw('sum(amount) as total'), 'department_id')
		->groupby('department_id')
		->get();

		return response()->json($json);
	}

	public function getPaymentMethodsChart(Request $request){
		$json = Payment::with('type')
		->select(DB::raw('count(id) as payments'), DB::raw('sum(amount) as total'), 'type_sale_id')
		->groupby('type_sale_id')
		->get();

		return response()->json($json);
	}

	public function getTopSellers(Request $request){
		$packages = Sale::with('cat_package')
		->where('primary_id','<>', null)
		->where('package_id','<>', null)
		->where('is_cancel', 0)
		->select(DB::raw('count(id) as sales'), DB::raw('sum(amount) as total'), 'package_id')
		->groupby('package_id')
		->orderBy('total', 'DESC')
		->limit(5)
		->get();

		$services = Sale::with('cat_service')
		->where('primary_id','<>', null)
		->where('service_id','<>', null)
		->where('is_cancel', 0)
		->select(DB::raw('count(id) as sales'), DB::raw('sum(amount) as total'), 'service_id')
		->groupby('service_id')
		->orderBy('total', 'DESC')
		->limit(5)
		->get();

		$products = Sale::with('cat_product')
		->where('primary_id','<>', null)
		->where('product_id','<>', null)
		->where('is_cancel', 0)
		->select(DB::raw('sum(count) as units'), DB::raw('sum(amount) as total'), 'product_id')
		->groupby('product_id')
		->orderBy('total', 'DESC')
		->limit(5)
		->get();

		return response()->json([
			'packages' => $packages,
			'services' => $services,
			'products' => $products,
		]);
	}

	public function getRecentActivity(Request $request){
		$sales = Sale::with('client', 'cat_service', 'cat_package', 'cat_product')
		->where('primary_id','<>', null)
		->orderBy('created_at', 'DESC')
		->limit(5)
		->get()
		->map(function($sale) {
			$concept = $sale->cat_service ? $sale->cat_service->name : ($sale->cat_package ? $sale->cat_package->name : ($sale->cat_product ? $sale->cat_product->name : 'Venta'));
			return [
				'type' => 'sale',
				'title' => 'Venta registrada',
				'description' => ($sale->client ? $sale->client->name . ' ' . $sale->client->lastname : 'Cliente') . ' - ' . $concept,
				'amount' => intval($sale->amount),
				'created_at' => $sale->created_at,
			];
		});

		$payments = Payment::with('type', 'sale.client')
		->orderBy('created_at', 'DESC')
		->limit(5)
		->get()
		->map(function($payment) {
			return [
				'type' => 'payment',
				'title' => 'Pago recibido',
				'description' => ($payment->sale && $payment->sale->client ? $payment->sale->client->name . ' ' . $payment->sale->client->lastname : 'Venta') . ' - ' . ($payment->type ? $payment->type->name : 'Metodo de pago'),
				'amount' => intval($payment->amount),
				'created_at' => $payment->created_at,
			];
		});

		$purchases = Purchase::with('provider')
		->orderBy('created_at', 'DESC')
		->limit(5)
		->get()
		->map(function($purchase) {
			return [
				'type' => 'purchase',
				'title' => 'Compra registrada',
				'description' => $purchase->provider ? $purchase->provider->business_name : ($purchase->name_product ?: 'Compra'),
				'amount' => intval($purchase->amount),
				'created_at' => $purchase->created_at,
			];
		});

		$activity = $sales->merge($payments)->merge($purchases)
		->sortByDesc('created_at')
		->values()
		->take(8);

		return response()->json($activity);
	}

	public function getDashboardAlerts(Request $request){
		$alerts = [];

		$pendingSales = Sale::where('primary_id','<>', null)
		->where('is_cancel', 0)
		->where('is_paid', 0);
		$pendingCount = (clone $pendingSales)->count();
		$pendingAmount = intval((clone $pendingSales)->sum('balance'));

		if($pendingCount > 0){
			$alerts[] = [
				'type' => 'payment',
				'level' => 'danger',
				'message' => $pendingCount . ' ventas tienen saldo pendiente',
				'value' => $pendingAmount,
			];
		}

		$lowProducts = ProductInventory::with('product')
		->where('count', '<=', 5)
		->limit(3)
		->get();
		foreach($lowProducts as $inventory){
			$alerts[] = [
				'type' => 'inventory',
				'level' => 'warning',
				'message' => ($inventory->product ? $inventory->product->name : 'Producto') . ' tiene ' . $inventory->count . ' unidades',
				'value' => intval($inventory->count),
			];
		}

		$lowPills = PillInventory::with('pill')
		->where('count', '<=', 5)
		->limit(3)
		->get();
		foreach($lowPills as $inventory){
			$alerts[] = [
				'type' => 'inventory',
				'level' => 'warning',
				'message' => ($inventory->pill ? $inventory->pill->name : 'Pastilla') . ' tiene ' . $inventory->count . ' unidades',
				'value' => intval($inventory->count),
			];
		}

		$packagesNearFinish = SpaPackage::with('type', 'tracking')
		->where('is_completed', 0)
		->get()
		->filter(function($package){
			return $package->type && $package->tracking && (($package->type->session_count - $package->tracking->count()) <= 1);
		})
		->take(3);

		foreach($packagesNearFinish as $package){
			$alerts[] = [
				'type' => 'package',
				'level' => 'info',
				'message' => ($package->type ? $package->type->name : 'Paquete') . ' esta por completar sesiones',
				'value' => $package->tracking->count(),
			];
		}

		return response()->json($alerts);
	}
}
