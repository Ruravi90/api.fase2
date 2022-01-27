<?php

namespace fase2\Http\Controllers;
use fase2\Sale;
use fase2\SellingElements;
use fase2\Department;
use fase2\Client;
use fase2\User;
use fase2\Package;
use fase2\CatPackage;
use fase2\PackageTracking;
use fase2\Payment;
use fase2\PillInventory;
use fase2\ProductInventory;
use fase2\SaleAdditional;
use Carbon\Carbon;
use DateTime;
use File;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Exception\HttpResponseException;
use fase2\Http\Requests\SalePostRequest;
use Illuminate\Support\Facades\DB;
use fase2\Log;
/**
 * @resource Example
 *
 * Sale description
 */
class SaleController extends Controller
{
	/**
	 * @return view
	 *
	*/
   	public function index(Request $request){
		return view('sale.index');
	}

    /**
	 * @return array
	 *
	*/
	public function getAll() {
		$sale = Sale::where('primary_id',null)->with(['department','client','sales'=>function($query){
			$query->with(['department','client', 'responsible', 'type', 'cat_package', 'cat_service', 'cat_pill', 'cat_product']);
		}])->get();
		return response($sale, 200)->header('Content-Type', 'application/json');
	}
/**
	 * @return array
	 *
	*/
	public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('perPage')){
			$perPage = $request->get('perPage');
		}

		$isPaid = $request->get('isPaid');

		
		switch($isPaid){
			case 0:
			$sales = Sale::with([
				'department',
				'client',
				'responsible',
				'type',
				'user',
				'sales' => function ($q) use ($isPaid) {
					$q->where('is_paid', $isPaid);
				},
				'sales.department',
				'sales.cat_package',
				'sales.cat_service',
				'sales.cat_pill',
				'sales.type',
				'sales.cat_product',
				'sales.payments'])
			->where('primary_id', null)
			->where('is_paid', $isPaid)
			->where('is_cancel', 0)
			->orderBy('updated_at', 'desc')->paginate($perPage);
			return response($sales, 200)->header('Content-Type', 'application/json');
			break;
			case 1:
				$sales = Sale::with([
					'department',
					'client',
					'responsible',
					'type',
					'user',
					'sales' => function ($q) use ($isPaid) {
						$q->where('is_paid', $isPaid);
					},
					'sales.department',
					'sales.cat_package',
					'sales.cat_service',
					'sales.cat_pill',
					'sales.type',
					'sales.cat_product',
					'sales.payments'])
				->where('primary_id', null)
				->orderBy('updated_at', 'desc')->paginate($perPage);
				return response($sales, 200)->header('Content-Type', 'application/json');
			break;
			case 2:
				//$from = date('Y-m-d' . ' 00:00:01', time());
				$salesForCout = Sale::select('cute_date')
				//->where('cute_date', '>=', $from)
				->groupBy('cute_date')
				->where('is_cute', 1)
				->orderBy('cute_date', 'desc')
				->paginate($perPage);

				return response($salesForCout, 200)->header('Content-Type', 'application/json');
			break;
			case 3:
			$sales = Sale::with([
				'department',
				'client',
				'responsible',
				'type',
				'user',
				'sales' => function ($q) use ($isPaid) {
					$q->where('is_paid', $isPaid);
				},
				'sales.department',
				'sales.cat_package',
				'sales.cat_service',
				'sales.cat_pill',
				'sales.type',
				'sales.cat_product',
				'sales.payments'])
			->where('primary_id', null)
			->where('is_cancel', 1)
			->orderBy('updated_at', 'desc')->paginate($perPage);
			return response($sales, 200)->header('Content-Type', 'application/json');
			break;
		}

		
	}
	/**
	 * @return array
	 *
	*/
	public function findId($id){
		$sale = Sale::with(
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.type',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.cat_product')
		->find($id);
		
		return response($sale, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * @return array
	 *
	*/
	public function getForDay(){

		$from = date('Y-m-d' . ' 00:00:00', time()); 
		$sales = Sale::with(
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.type',
			'sales.cat_product')
		->where('primary_id', null)
		->where('updated_at','>=',$from)
		->where('is_cute', 0)
		->orderBy('primary_id', 'desc')
		->get();
		
		return response($sales, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * @return array
	 *
	*/
	public function getSalesUserDay($user_id){
		$from = date('Y-m-d' . ' 00:00:00', time()); 
		$sales = Sale::with(
			['department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.type',
			'sales.cat_product',
			'sales.payments'=>function($query){
				$from = date('Y-m-d' . ' 00:00:00', time()); 
				$query->where('created_at','>=',$from);
			}])
		->where('primary_id', null)
		->where('updated_at','>=',$from)
		->where('is_cute', 0)
		->where('user_id', $user_id)
		->orderBy('primary_id', 'desc')
		->get();
		
		return response($sales, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * @return array
	 *
	*/
	public function cuteSales(Request $request) {
		if(!$request->has('user_id')){
			return response('No se encontro el parametro user_id',404)
			->header('Content-Type', 'application/json');
		}

		$from = date('Y-m-d' . ' 00:00:01', time());

		$departmanets = Department::all()->toArray();;

		$salesGruop = Sale::with(['client','responsible','sales' => function($q)  use($from){
			$q->with([
				'cat_package',
				'cat_service',
				'cat_pill',
				'cat_product',
				'type',
				'payments',
				'payments'=>function($q2) use($from){
					$q2->where('updated_at','>=',$from);
				},
				'payments.type',
			])
			->where('is_cancel', 0);
		}])
		->where('primary_id', null)
		->where('is_cute', 0)
		->where('is_cancel', 0)
		->where('updated_at','>=',$from)
		->get();

		$json = array();
		$count = 0;

		foreach ($departmanets as $_department){
			$json[$count] = $_department;

			$jsonPrimary = array();
			$countPrimary = 0;
			foreach ($salesGruop as $group){

				$primary = Sale::find($group['id']);
				$primary->cute_user_id = $request->get('user_id');
				$primary->is_cute = 1;
				$primary->cute_date = Carbon::now();
				$primary->save();

				$jsonSales = array();
				$countSales = 0;
				foreach ($group->sales as $sale){
					if($_department['id'] == $sale['department_id']){
						if($sale['is_cancel'] == 0){
							$jsonSales[$countSales] = $sale;
							$countSales++;
						}
					}
				}

				if(count($jsonSales) > 0 && $group['is_cancel'] == 0){
					$jsonPrimary[$countPrimary]['sales'] = $jsonSales;
					$jsonPrimary[$countPrimary]['client'] = $group->client;
					$jsonPrimary[$countPrimary]['responsible'] = $group->responsible;
					$jsonPrimary[$countPrimary]['client'] = $group->client;
					$countPrimary++;
				}
			}

			if(count($jsonPrimary) > 0){
				$json[$count]['sales'] = $jsonPrimary;
				$count++;
			}
		}
		
		return response($json, 200)->header('Content-Type', 'application/json');
	}


	public function getCuteSales(Request $request) {
		$from = date('Y-m-d' . ' 00:00:01', time());
		if($request->has('date')){
			$from = $request->get('date');
		}

		$dt = DateTime::createFromFormat('Y-m-d H:i:s',$from);
		$start =  Carbon::instance($dt)->startOfDay();
		$end = Carbon::instance($dt);

		$departmanets = Department::all()->toArray();;

		$salesGruop = Sale::with(['client','responsible','sales' => function($q)  use($start,$end){
			$q->with([
				'cat_package',
				'cat_service',
				'cat_pill',
				'cat_product',
				'type',
				'payments',
				'payments'=>function($q2) use($start){
					$q2->where('updated_at','>=',$start->toDateTimeString());
				},
				'payments.type',
			])->where('is_cancel', 0);
		}])
		->where('primary_id', null)
		->where('is_cute', 1)
		->where('is_cancel', 0)
		->where('cute_date','=',$end->toDateTimeString())
		->get();

		$json = array();
		$count = 0;

		foreach ($departmanets as $_department){
			$json[$count] = $_department;

			$jsonPrimary = array();
			$countPrimary = 0;
			foreach ($salesGruop as $group){

				$primary = Sale::find($group['id']);
				$primary->cute_user_id = $request->get('user_id');
				$primary->is_cute = 1;
				$primary->cute_date = Carbon::now();
				$primary->save();

				$jsonSales = array();
				$countSales = 0;
				foreach ($group->sales as $sale){
					if($_department['id'] == $sale['department_id']){
						if($sale['is_cancel'] == 0){
							$jsonSales[$countSales] = $sale;
							$countSales++;
						}
					}
				}

				if(count($jsonSales) > 0 && $group['is_cancel'] == 0){
					$jsonPrimary[$countPrimary]['sales'] = $jsonSales;
					$jsonPrimary[$countPrimary]['client'] = $group->client;
					$jsonPrimary[$countPrimary]['responsible'] = $group->responsible;
					$jsonPrimary[$countPrimary]['client'] = $group->client;
					$countPrimary++;
				}
			}

			if(count($jsonPrimary) > 0){
				$json[$count]['sales'] = $jsonPrimary;
				$count++;
			}
		}

		return response($json, 200)->header('Content-Type', 'application/json');
	}


	/**
	 * @return array
	 *
	*/
	public function add(Request $request){
		$primary = new Sale;
		$primary->department_id = 0;//$request->get('sales')[0]['department_id'];
		$primary->responsible_id = $request->get('sales')[0]['responsible_id'];
		$primary->client_id = $request->get('sales')[0]['client_id'];
		$primary->user_id = $request->get('sales')[0]['user_id'];
		$primary->type_sale_id = 0;
		$primary->balance = 0;
		$primary->price = 0;
		$primary->amount = 0;
		$primary->count = 0;
		$primary->is_paid = 0;
		$primary->save();

		foreach ($request->get('sales') as $_sale) {
			$sale = new Sale;
			$sale->department_id = $_sale['department_id'];
			$sale->responsible_id = $_sale['responsible_id'];
			$sale->client_id = $_sale['client_id'];
			$sale->user_id = $_sale['user_id'];
			$sale->primary_id = $primary->id;
			$sale->type_sale_id = $_sale['type_sale_id'];
			$sale->count = $_sale['count'];
			$sale->price = $_sale['price'];

			$total = $_sale['price'] * $_sale['count'];
			$discount = 0;
			
			if(isset($_sale['discount'])){
				$sale->discount = $_sale['discount'];
				$discount = (($sale->discount * $total) / 100);
			}
	    		
			$sale->total = $total - $discount;
			$sale->amount = $_sale['amount'];
			$sale->partial_payment = $_sale['amount'];

			$sale->balance = ($sale->total - $sale->amount);
			$sale->is_paid = (($sale->balance == 0) ? 1 : 0);

			if(isset($_sale['description']))
	    		$sale->description = $_sale['description'];
	    	if(isset($_sale['product_id']))
				$sale->product_id = $_sale['product_id'];
			if(isset($_sale['service_id']))
				$sale->service_id = $_sale['service_id'];
			if(isset($_sale['package_id']))
				$sale->package_id = $_sale['package_id'];
			if(isset($_sale['pill_id']))
				$sale->pill_id = $_sale['pill_id'];

			$sale->save();

			$payment = new Payment;
	        $payment->sale_id = $sale->id;
			$payment->user_id = $sale->user_id;
			$payment->responsible_id = $_sale['responsible_id'];
			$payment->type_sale_id = $_sale['type_sale_id'];
	        $payment->amount = $_sale['amount'];
			$payment->save();
			

			if(isset($_sale['package_id'])){
				$package = new Package;
				$package->sale_id = $sale->id;
			 	$package->client_id = $sale->client_id;
	            $package->cat_package_id =  $_sale['package_id'];
	            $package->is_completed = false;
				$package->save();
			}

			if(isset($_sale['service_id'])){
				
			}

			if(isset($_sale['pill_id'])){
				$pillInventory = PillInventory::where('pill_id',$_sale['pill_id'])->first();
				$pillInventory->count = ($pillInventory->count - $sale->count );
				$pillInventory->save();
			}

			if(isset($_sale['product_id'])){
				$productInventory = ProductInventory::where('product_id',$_sale['product_id'])->first();
				$productInventory->count = ($productInventory->count - $sale->count );
				$productInventory->save();
			}

			foreach ($_sale['additionals'] as $_additional) {
				$additional = new SaleAdditional;
				$additional->sale_id = $sale->id;

				if(isset($_additional['pill_id'])){
					$additional->pill_id = $_additional['pill_id'];
					$pillInventory = PillInventory::where('pill_id',$_additional["pill_id"])->first();
					$pillInventory->count = ($pillInventory->count - $_additional["count"]);
					$pillInventory->save();
				}
					
				if(isset($_additional['product_id'])){
					$additional->product_id = $_additional['product_id'];
					$productInventory = ProductInventory::where('product_id',$_additional["product_id"])->first();
					$productInventory->count = ($productInventory->count - $_additional["count"]);
					$productInventory->save();
				}
				
				$additional->count =  $_additional['count'];
				$additional->save();
			}
		}

		$total =  Sale::where('primary_id',$primary->id)->sum('total');
		$balance = Sale::where('primary_id',$primary->id)->sum('amount');
		
		$primary = Sale::find($primary->id);

		$primary->total = $total;
		$primary->balance = ($total - $balance);
		$primary->is_paid = (($primary->balance == 0) ? 1 : 0);

		$primary->save();

		return response($primary,200)->header('Content-Type', 'application/json');
	}
	/**
	 * @return array
	 *
	*/
	public function addSales(Request $request){
		return response($sale, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * @return array
	 *
	*/
    public function update($id,Request $request){// se envia el id a $client 

    	return response($provider, 200)->header('Content-Type', 'application/json');
	}
	
/**
	 * @param  Guard  $auths
	 * @return void
	*/
	public function cancel($id, Request $request){// se envia el id a $client 

		$userId = $request->get('user_id');
		$Sale = Sale::with(['sales'])
		->where('id',$id)
		->where('is_cancel',0)
		->first();

		//Log::info('Log message ', $Sale);
		if($Sale->primary_id == null){
			foreach ($Sale['sales'] as $_s) {
				$update = Sale::find($_s->id); 
				if($update->product_id != null){
					$productInventory = ProductInventory::where('product_id',$update->product_id)->first();
					$productInventory->count = ($productInventory->count + $update->count );
					$productInventory->save();
				}else if($update->package_id != null){
					$package = CatPackage::with(['complements'])->find($update->package_id);
					foreach ($package['complements'] as $complement) {
						$productInventory = ProductInventory::where('product_id',$complement['product_id'])->first();
						$productInventory->count = ($productInventory->count + $complement['count'] );
						$productInventory->save();
					}	
				}

				$update->is_cancel = 1;
				$update->save();
			}

			$log = new Log;
			$log->user_id = $userId;
			$log->table = 'sales';
			$log->table_id = $Sale->id;
			$log->description = 'Se cancelo la venta pricipal, todas las ventas dependientes a esta tambien fueron cancelados';
			$log->save();
		}
		else{
			if($Sale['product_id'] != null){
				$productInventory = ProductInventory::where('product_id',$Sale['product_id'])->first();
				$productInventory->count = ($productInventory->count + $Sale['count'] );
				$productInventory->save();
			}
			else if($Sale['package_id'] != null){
				$package = CatPackage::with(['complements'])->find($Sale['package_id']);
				foreach ($package['complements'] as $complement) {
					$productInventory = ProductInventory::where('product_id',$complement['product_id'])->first();
					$productInventory->count = ($productInventory->count + $complement['count'] );
					$productInventory->save();
				}	
			}

			$log = new Log;
			$log->user_id = $userId;
			$log->table = 'sales';
			$log->table_id = $Sale->id;
			$log->description = 'Fue cancelado la venta con id ' + $Sale->id;
			$log->save();
		}

		$Sale->is_cancel = 1;
		$Sale->save();

		//Log::info('Log cancel sale ', $Sale);

    	return response($Sale, 200)->header('Content-Type', 'application/json');
    }

	/**
	 * @param  Guard  $auth
	 * @return void
	*/
    public function delete($id){// se envia el id a $client 
		$Sale = Sale::find($id);  
		$primary_id = $Sale->primary_id;

		if($Sale->sales()->count()>0){
			foreach ($Sale->sales() as $_sale) {
				if($_sale->product_id != null){
					$productInventory = ProductInventory::where('product_id',$_sale['product_id'])->first();
					$productInventory->count = ($productInventory->count + $_sale->count );
					$productInventory->save();
				}
			}
		}else{
			if($Sale->product_id != null){
				$productInventory = ProductInventory::where('product_id',$Sale['product_id'])->first();
				$productInventory->count = ($productInventory->count + $Sale->count );
				$productInventory->save();
			}

			$Sale->additionals()->delete();
			$Sale->sales()->delete();
			$Sale->payments()->delete();
			$Sale->packages()->delete();	
			$Sale->delete();
		}

		if($primary_id == null && $Sale->sales()->count()>0){
			$Sale = Sale::find($primary_id);  
			$Sale->additionals()->delete();
			$Sale->sales()->delete();
			$Sale->payments()->delete();
			$Sale->packages()->delete();	
			$Sale->delete();
		}

    	return response()->json(null, 204);
    }
}
