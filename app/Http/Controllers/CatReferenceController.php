<?php
namespace App\Http\Controllers;
use App\Models\CatReference;
use Illuminate\Http\Request;
use App\Http\Requests\CatReferenceRequest;
use Illuminate\Support\Facades\DB;

class CatReferenceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cat_references/paginate",
     *     tags={"cat_references"},
     *     summary="Get cat references per paginate",
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
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }

        $results = DB::table('cat_references');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_references",
     *     tags={"cat_references"},
     *     summary="Get all cat references",
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
        $references =  CatReference::all();
        return response($references, 200)
        ->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_references/{id}",
     *     tags={"cat_references"},
     *     summary="Get cat references",
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
        $reference = CatReference::find($id);
        return response($reference, 200)
        ->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/cat_references/{id}",
     *     tags={"cat_references"},
     *     summary="Add cat references",
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
		CatReference::create(
            $request->only('name')
        );
		return response('success', 200)
        ->header('Content-Type', 'application/json');
	}

    /**
     * @OA\Put(
     *     path="/api/cat_references/{id}",
     *     tags={"cat_references"},
     *     summary="Update cat references",
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
    	$reference = CatReference::find($id);
		$reference->update(
            $request->only('name')
        );
    	return response('success', 200)
        ->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Delete(
     *     path="/api/cat_references/{id}",
     *     tags={"cat_references"},
     *     summary="Delete cat references",
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
        $reference = CatReference::find($id);
		$reference->delete();
    	return response('success', 200)
        ->header('Content-Type', 'application/json');
    }
}

