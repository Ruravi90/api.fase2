<?php
namespace App\Http\Controllers;
use App\Models\CatProduct;
use Illuminate\Http\Request;
use App\Http\Requests\CatProductRequest;
use Illuminate\Support\Facades\DB;

class CatProductController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/cat_products/paginate",
     *     tags={"cat_products"},
     *     summary="Get all cat_product per paginate",
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

        $results = DB::table('cat_products');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_products",
     *     tags={"cat_products"},
     *     summary="Get all cat_products",
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
    public function getAll(){
        $products =  CatProduct::with(['inventory'])->get();
        return response($products, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_products/{id}",
     *     tags={"cat_products"},
     *     summary="Get cat_product",
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
        $product = CatProduct::find($id);
        return response($product, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/cat_products",
     *     tags={"cat_products"},
     *     summary="Add cat_product",
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
        $product =new CatProduct;
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->save();
        return ['success' => true];
	}

    /**
     * @OA\Put(
     *     path="/api/cat_products/{id}",
     *     tags={"cat_products"},
     *     summary="Update cat_product",
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
    	$product = CatProduct::find($id);
		$product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->save();
    	return ['success' => true];
    }
    /**
     * @OA\Delete(
     *     path="/api/cat_products/{id}",
     *     tags={"cat_products"},
     *     summary="Delete cat_product",
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
        $product = CatProduct::find($id);
		$product->delete();
    	return ['success' => true];
    }
}

