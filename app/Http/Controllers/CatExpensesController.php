<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatExpense;
use Illuminate\Support\Facades\DB;

class CatExpensesController extends Controller
{
/**
     * @OA\Post(
     *     path="/api/cat_expenses/paginate",
     *     tags={"cat_expenses"},
     *     summary="Get cat_expenses per paginate",
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

        $results = DB::table('cat_expenses');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_expenses",
     *     tags={"cat_expenses"},
     *     summary="Delete cat_expenses",
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
        $expences = CatExpense::all();
        return response($expences, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_expenses/{id}",
     *     tags={"cat_expenses"},
     *     summary="Get cat_expenses",
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
        $expence = CatExpense::find($id);
        return response($expence, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/cat_expenses",
     *     tags={"cat_expenses"},
     *     summary="Add cat_expenses",
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
        $expence = new CatExpense;
        $expence->name = $request->get('name');
        $expence->save();

		return response($expence, 200)->header('Content-Type', 'application/json');
	}
    /**
     * @OA\Put(
     *     path="/api/cat_expenses/{id}",
     *     tags={"cat_expenses"},
     *     summary="Update cat_expenses",
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
    	$expence = CatExpense::find($id);
		$expence->name = $request->get('name');
        $expence->save();
    	return response($expence, 200)->header('Content-Type', 'application/json');
    }
    /**
     * @OA\Delete(
     *     path="/api/cat_expenses/{id}",
     *     tags={"cat_expenses"},
     *     summary="Delete cat_expenses",
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
        $expence = CatExpense::find($id);
		$expence->delete();
    	return response('Ok', 200)->header('Content-Type', 'application/json');
    }
}
