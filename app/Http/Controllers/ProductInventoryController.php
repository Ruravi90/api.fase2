<?php

namespace App\Http\Controllers;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{
	/**
     * @OA\Post(
     *     path="/api/products_inventory",
     *     tags={"products_inventory"},
     *     summary="Add products_inventory",
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
        $inventory = new ProductInventory;
		$inventory->product_id = $request->get('product_id');
		$inventory->count = $request->get('count');
		$inventory->save();
		return ['success' => true];
	}
    /**
     * @OA\Put(
     *     path="/api/products_inventory/{id}",
     *     tags={"products_inventory"},
     *     summary="Update products_inventory",
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
		$inventory = ProductInventory::find($id);
		$inventory->product_id = $request->get('product_id');
		$inventory->count = $request->get('count');
		$inventory->save();
    	return ['success' => true];
    }
/**
     * @OA\Delete(
     *     path="/api/products_inventory/{id}",
     *     tags={"products_inventory"},
     *     summary="Delete products_inventory",
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
    	$user = ProductInventory::find($id);
		$user->delete();
    	return ['success' => true];
    }
    /**
     * @OA\Get(
     *     path="/api/products_inventory",
     *     tags={"products_inventory"},
     *     summary="Get all products_inventory",
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
        $inventory = ProductInventory::with('product')->get();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/products_inventory/{id}",
     *     tags={"products_inventory"},
     *     summary="Get products_inventory",
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
        $inventory = ProductInventory::with('product')->find($id);
        return response($inventory, 200)->header('Content-Type', 'application/json');
	}
/**
     * @OA\Get(
     *     path="/api/products_inventory/product/{id}",
     *     tags={"products_inventory"},
     *     summary="Get products_inventory per product",
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
	public function forProduct($id){
        $inventory = ProductInventory::with('product')->where('product_id',$id)->first();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

}
