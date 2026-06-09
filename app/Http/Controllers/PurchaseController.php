<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\CatExpense;
use App\Models\CatConcept;
use App\Models\CatPill;
use App\Models\CatProduct;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
	public function index()
	{
		return view('purchase.index');
	}

	public function pendingIndex()
	{
		return view('purchase_pending.index');
	}


	 // Api rest

	public function getPaginate(Request $request) {
		//per_page
		
		$perPage = 15;
		if($request->has('perPage')){
			$perPage = $request->get('perPage');
        }
        
		$purchase = Purchase::with(['user','purchases'=>function($query){
			$query->with(['department','provider','cat_expense','cat_concept','cat_pill','cat_product']);
		}])
		->where('purchase_id',null);

		if(is_numeric($request->get('isPaid'))){
			$purchase = $purchase->where('is_paid',$request->get('isPaid'))
			->orderBy('created_at', 'desc')->paginate($perPage);
		}
		else {
			$from = date('Y-m-d' . ' 00:00:00', time()); 
			$purchase = $purchase->where('created_at','>=',$from)
			->orderBy('created_at', 'desc')->paginate($perPage);
		}

		return response($purchase, 200)->header('Content-Type', 'application/json');
	}

	public function getAll(){
		$purchases = Purchase::with(['user','purchases'=>function($query){
			$query->with(['department','provider','cat_expense','cat_concept','cat_pill','cat_product']);
		}])
		->where('purchase_id',null)
		->get();
		return response($purchases, 200)->header('Content-Type', 'application/json');
	}

	public function getPending(Request $request){
		$isPaid = $request->get('is_paid');
		$purchases = Purchase::with(['user','purchases'=>function($query){
			$query->with(['department','provider','cat_expense','cat_concept','cat_pill','cat_product']);
		}])
		->where('purchase_id',null)
		->where('is_paid',false)
		->get();
		return response($purchases, 200)->header('Content-Type', 'application/json');
	}

	public function find($id){ 
		$purchases = Purchase::with(['user','purchases'=>function($query){
			$query->with(['department','provider','cat_expense','cat_concept','cat_pill','cat_product']);
		}])->find($id);
		return response($purchases, 200)->header('Content-Type', 'application/json');
	}

	public function add(Request $request){
		//$currentuserid= Auth::user()->id;
		$purchase = new Purchase;
		$purchase->department_id = $request->get('purchases')[0]['department_id'];
        $purchase->user_id = $request->get('purchases')[0]['user_id'];
        $purchase->expence_id = 0;
        $purchase->concept_id = 0;
		$purchase->count = 0;
		$purchase->description = 0;
		$purchase->amount = 0;
		$purchase->save();

		foreach ($request->get('purchases') as $_purchase) {
			$this->addPurchase($purchase->id,$_purchase);
		}

		$purchaseCount = Purchase::where('purchase_id',$purchase->id)
		->where('is_paid',false)->count();

		if($purchaseCount == 0){
			$purchase = Purchase::find($purchase->id);
			$purchase->is_paid = true;
			$purchase->save();
		}

		return response(["success" => "Ok","Purchase"=>$purchase], 200)->header('Content-Type', 'application/json'); 
	}


	public function addPurchase($purchase_id,$_purchase){
		//$currentuserid = Auth::user()->id;
		if(isset($_purchase['name_expense'])){
			$expense = new CatExpense;
			$expense->name = $_purchase['name_expense'];
			$expense->save();
		}
		else if (isset($_purchase['expense_id']))
			$expense = CatExpense::find($_purchase['expense_id']);

		if(isset($_purchase['name_concept'])){
			$concept = new CatConcept;
			$concept->name = $_purchase['name_concept'];
			$concept->save();
		}
		else if (isset($_purchase['concept_id']))
			$concept = CatConcept::find($_purchase['concept_id']);
		
        $purchase = new Purchase;
		$purchase->department_id = $_purchase['department_id'];
		$purchase->provider_id = $_purchase['provider_id'];
        $purchase->user_id = $_purchase['user_id'];
        $purchase->purchase_id = $purchase_id;
        $purchase->expence_id = $expense->id;
        $purchase->concept_id = $concept->id;
		$purchase->count = 0;

		$pill = null;
		if(isset($_purchase['name_pill'])){
			$pill = new CatPill;
			$pill->name = $_purchase['name_pill'];
			$pill->price = 0;
			$pill->save();
		}
		else if (isset($_purchase['pill_id']))
			$pill = CatPill::find($_purchase['pill_id']);

        if($pill != null){
			$purchase->pill_id = $pill->id;
			$purchase->count = $_purchase['pill_count'];
			$pillInventary = PillInventory::where('pill_id',$pill->id)->first();
			if($pillInventary != null){
				$pillInventary->count = ($pillInventary->count + $purchase->count);
			}
			else{
				$pillInventary = new PillInventory;
				$pillInventary->pill_id = $pill->id;
				$pillInventary->count = ($pillInventary->count + $purchase->count);
			}

			$pillInventary->save();
		}
		
		$product = null;
		if(isset($_purchase['name_product'])){
			$product = new CatProduct;
			$product->name = $_purchase['name_product'];
			$product->price = 0;
			$product->save();
		}
		else if (isset($_purchase['product_id']))
			$product = CatProduct::find($_purchase['product_id']);
			
		if($product != null){
			$purchase->product_id = $product->id;
			$purchase->count = $_purchase['product_count'];
			$productInventory = ProductInventory::where('product_id',$product->id)->first();
			if($productInventory != null){
				$productInventory->count = ($productInventory->count + $purchase->count);
			}
			else{
				$productInventory = new ProductInventory;
				$productInventory->product_id = $product->id;
				$productInventory->count = ($productInventory->count + $purchase->count);
			}
			$productInventory->save();
		}

	    $purchase->description = '';
		$purchase->amount = $_purchase['amount'];
		$purchase->is_paid = $_purchase['is_paid'];
		$purchase->save();
	}

    public function update($id,Request $request){// se envia el id a $client 
		$purchase = Purchase::find($id);
		$purchase->product_id = $request->get('product_id');
		$purchase->count = $request->get('count');
		$purchase->save();
    	return response($purchase, 200)->header('Content-Type', 'application/json');
	}
	
	public function pay($id,Request $request){// se envia el id a $client 
		$purchase = Purchase::find($id);
		$purchase->is_paid = $request->get('is_paid');
		$purchase->save();

		$primary = $purchase->purchase_id;
		$purchaseCount = Purchase::where('purchase_id',$primary)
		->where('is_paid',false)->count();

		if($purchaseCount == 0){
			$primaryPurchase = Purchase::find($primary);
			$primaryPurchase->is_paid = true;
			$primaryPurchase->save();
		}

    	return response($purchase, 200)->header('Content-Type', 'application/json');
	}
	
	/**
	 * @param  Guard  $auth
	 * @return void
	*/
	public function cancel($id,Request $request){// se envia el id a $client 
		$Purchase = Purchase::find($_purchase->id); 
		$userId = $request->get('user_id');

		if($Purchase->primary_id == null && $Purchase->purchases()->count() > 0){
			foreach ($Purchase->purchases() as $_purchase) {
				$pur = Purchase::find($_purchase->id); 
				if($pur->product_id != null){
					$productInventory = ProductInventory::where('product_id',$pur->product_id)->first();
					$productInventory->count = ($productInventory->count - $purchase->count );
					$productInventory->save();
				}
				$pur->is_cancel = 1;
				$pur->save();
			}

			$log = new Log;
			$log->user_id = $userId;
			$log->table = 'purchases';
			$log->table_id = $Purchase->id;
			$log->description = 'Se cancelo el egreso pricipal, todos los egresos dependientes a este tambien fueron cancelados';
			$log->save();
		}
		else{
			if($Purchase->product_id != null){
				$productInventory = ProductInventory::where('product_id',$Purchase->product_id)->first();
				$productInventory->count = ($productInventory->count - $Purchase->count );
				$productInventory->save();
			}

			$log = new Log;
			$log->user_id = $userId;
			$log->table = 'purchases';
			$log->table_id = $Purchase->id;
			$log->description = 'Fue cancelado el egreso con id ' +$Purchase->id;
			$log->save();
		}

		$Purchase->is_cancel = 1;
		$Purchase->save();

    	return response()->json(null, 204);
    }

    public function delete($id){
    	$purchase = Purchase::find($id);
		$purchase->delete();
    	return response('Ok', 200)->header('Content-Type', 'application/json');
    }
}
