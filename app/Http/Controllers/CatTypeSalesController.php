<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatTypeSale;
use Illuminate\Support\Facades\DB;

class CatTypeSalesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cat_type_sales/paginate",
     *     tags={"cat_type_sales"},
     *     summary="Get cat type sales per paginate",
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
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }

        $results = DB::table('cat_type_sale');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_type_sales",
     *     tags={"cat_type_sales"},
     *     summary="Get all cat type sales",
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
        $type =  CatTypeSale::all();
        return response($type, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_type_sales/{id}",
     *     tags={"cat_type_sales"},
     *     summary="Get cat type sales",
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
        $type = CatTypeSale::find($id);
        return response($type, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/cat_type_sales",
     *     tags={"cat_type_sales"},
     *     summary="Get cat type sales",
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
		CatTypeSale::create(
            $request->only('name')
        );
		return ['success' => true];
	}

    /**
     * @OA\Put(
     *     path="/api/cat_type_sales/{id}",
     *     tags={"cat_type_sales"},
     *     summary="Update cat type sales",
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
    	$type = CatTypeSale::find($id);
		$type->update(
            $request->only('name')
        );
    	return ['success' => true];
    }

    /**
     * @OA\Delete(
     *     path="/api/cat_type_sales/{id}",
     *     tags={"cat_type_sales"},
     *     summary="Delete cat type sales",
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
        $type = CatTypeSale::find($id);
		$type->delete();
    	return ['success' => true];
    }
}
