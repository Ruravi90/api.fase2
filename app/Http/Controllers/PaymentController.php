<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\CatPackage;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
	{
		return view('payment.index');
	}

	public function getAll(){
        $payments = Payment::with('user')->get();
        return response($payments, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $payments = Payment::with(
            "responsible",
            "type",
            "user",
            'sale',
            'sale.department',
            'sale.client',
            'sale.type',
			'sale.cat_package',
			'sale.cat_service',
			'sale.cat_pill',
			'sale.cat_product'
        )->find($id);
        return response($payments, 200)->header('Content-Type', 'application/json');
    }

    public function forSaleId($id){
        $payments = Payment::with(
            "responsible",
            "type",
            "user",
            'sale',
			'sale.department',
			'sale.cat_package',
			'sale.cat_service',
			'sale.cat_pill',
			'sale.type',
			'sale.cat_product'
        )
        ->where('sale_id' ,$id)->get();
        return response($payments, 200)->header('Content-Type', 'application/json');
    }

    public function add(Request $request)
    {
        $payment = new Payment;
        $payment->amount = $request->input("amount");
        $payment->sale_id = $request->input("sale_id");
        $payment->responsible_id = $request->input("responsible_id");
        $payment->type_sale_id = $request->input("type_sale_id");
        $payment->user_id = $request->input("user_id");
        $payment->save();

        $sale = Sale::find($payment->sale_id);
        $sale->partial_payment =  $payment->amount;
        $sale->amount = ($sale->amount + $payment->amount);

        $balance = Payment::where('sale_id',$sale->id)->sum('amount');
       
        $sale->balance = ($sale->total - $balance);

        if( $sale->balance == 0)
            $sale->is_paid = 1;

        $sale->save();

        $primary = Sale::find($sale->primary_id);

        $total =  Sale::where('primary_id',$primary->id)->sum('total');
		$balance = Sale::where('primary_id',$primary->id)->sum('amount');

        $primary->total = $total;
        $primary->balance = ($total - $balance);
        
		if($primary->balance == 0)
			$primary->is_paid = 1;
		else
			$primary->is_paid = 0;

        $primary->is_cute = 0;
        $primary->save();

        return response($payment,200)->header('Content-Type', 'application/json');
    }

    public function update($id, Request $request)
    {
        $payment              = Payment::find($id);
        $payment->amount        = $request->input("amount");
        $payment->sale_id        = $request->input("sale_id");
        $payment->save();
        return response($payment, 200)->header('Content-Type', 'application/json');
    }

    public function delete($id)
    {   
        $payment = Payment::find($id);
        $payment->delete();
        return ['success' => true];
    }
}
