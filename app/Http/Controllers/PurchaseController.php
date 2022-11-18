<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\CatExpense;
use App\Models\CatConcept;
use App\Models\CatPill;
use App\Models\CatProduct;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\Log;

class PurchaseController extends Controller
{
	/**
     * @OA\Post(
     *     path="/api/purchases/paginate",
     *     tags={"purchases"},
     *     summary="Get purchase per paginate",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/purchases",
     *     tags={"purchases"},
     *     summary="Get all purchases",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/purchases/{id}",
     *     tags={"purchases"},
     *     summary="Get purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
	public function find($id){
		$purchases = Purchase::with(['user','purchases'=>function($query){
			$query->with(['department','provider','cat_expense','cat_concept','cat_pill','cat_product']);
		}])->find($id);
		return response($purchases, 200)->header('Content-Type', 'application/json');
	}


    /**
     * @OA\Post(
     *     path="/api/purchases",
     *     tags={"purchases"},
     *     summary="Add purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/purchases/{id}",
     *     tags={"purchases"},
     *     summary="Update purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function update($id,Request $request){// se envia el id a $client
		$purchase = Purchase::find($id);
		$purchase->product_id = $request->get('product_id');
		$purchase->count = $request->get('count');
		$purchase->save();
    	return response($purchase, 200)->header('Content-Type', 'application/json');
	}

    /**
     * @OA\Post(
     *     path="/api/purchases/pay/{id}",
     *     tags={"purchases"},
     *     summary="Pay purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
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
     * @OA\Post(
     *     path="/api/purchases/cancel/{id}",
     *     tags={"purchases"},
     *     summary="Cancel purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
	public function cancel($id,Request $request){// se envia el id a $client
		$Purchase = Purchase::find($request->input("id"));
		$userId = $request->get('user_id');

		if($Purchase->primary_id == null && $Purchase->purchases()->count() > 0){
			foreach ($Purchase->purchases() as $_purchase) {
				$pur = Purchase::find($_purchase->id);
				if($pur->product_id != null){
					$productInventory = ProductInventory::where('product_id',$pur->product_id)->first();
					$productInventory->count = ($productInventory->count - $_purchase->count );
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

     /**
     * @OA\Delete(
     *     path="/api/purchases/{id}",
     *     tags={"purchases"},
     *     summary="Delete purchase",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function delete($id){
    	$purchase = Purchase::find($id);
		$purchase->delete();
    	return response('Ok', 200)->header('Content-Type', 'application/json');
    }
}
