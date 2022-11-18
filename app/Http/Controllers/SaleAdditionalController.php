<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleAdditional;

class SaleAdditionalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sale_additionals",
     *     tags={"sale_additionals"},
     *     summary="Get all sale_additionals",
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
		$additionals = SaleAdditional::with(['sale','cat_pill','cat_product'])->get();
		return response($additionals, 200)->header('Content-Type', 'application/json');
	}
/**
     * @OA\Get(
     *     path="/api/sale_additionals/{id}",
     *     tags={"sale_additionals"},
     *     summary="Get sale_additionals",
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
		$additional = SaleAdditional::with(['sale','cat_pill','cat_product'])->find($id);
		return response($additional, 200)->header('Content-Type', 'application/json');
    }
/**
     * @OA\Post(
     *     path="/api/sale_additionals",
     *     tags={"sale_additionals"},
     *     summary="Add sale_additionals",
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
        $additional = SaleAdditional::find($request->input("id"));
        $additional->sale_id = $request->get('sale_id');
        $additional->pill_id = $request->get('pill_id');
        $additional->product_id = $request->get('product_id');
        $additional->count = $request->get('count');
        $additional->save();
    }
/**
     * @OA\Put(
     *     path="/api/sale_additionals/{id}",
     *     tags={"sale_additionals"},
     *     summary="Update sale_additionals",
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
        $additional = SaleAdditional::find($id);
        $additional->pill_id = $request->get('pill_id');
        $additional->product_id = $request->get('product_id');
        $additional->count = $request->get('count');
        $additional->save();
    	return response($additional, 200)->header('Content-Type', 'application/json');
    }
/**
     * @OA\Delete(
     *     path="/api/sale_additionals/{id}",
     *     tags={"sale_additionals"},
     *     summary="Delete sale_additionals",
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
    public function delete($id){// se envia el id a $client
    	$provider = SaleAdditional::find($id);
    	$provider->delete();
    	return response("Ok", 200)->header('Content-Type', 'application/json');
    }
}
