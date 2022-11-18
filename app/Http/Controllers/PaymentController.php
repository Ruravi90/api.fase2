<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/payments",
     *     tags={"payments"},
     *     summary="Get all payment",
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
        $payments = Payment::with('user')->get();
        return response($payments, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     tags={"payments"},
     *     summary="Get payment",
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

    /**
     * @OA\Get(
     *     path="/api/payments/for_sale/{id}",
     *     tags={"payments"},
     *     summary="Get payment per sale",
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

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     tags={"payments"},
     *     summary="Delete payment",
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

    /**
     * @OA\Update(
     *     path="/api/payments/{id}",
     *     tags={"payments"},
     *     summary="Update payment",
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
    public function update(Request $request)
    {
        $payment              = Payment::find($request->input("id"));
        $payment->amount        = $request->input("amount");
        $payment->sale_id        = $request->input("sale_id");
        $payment->save();
        return response($payment, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Delete(
     *     path="/api/payments/{id}",
     *     tags={"payments"},
     *     summary="Delete payment",
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
    public function delete($id)
    {
        $payment = Payment::find($id);
        $payment->delete();
        return ['success' => true];
    }
}
