<?php
namespace App\Http\Controllers;
use App\Models\CatService;
use Illuminate\Http\Request;
use App\Http\Requests\CatServiceRequest;
use Illuminate\Support\Facades\DB;

class CatServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cat_services/paginate",
     *     tags={"cat_services"},
     *     summary="Get cat services per paginate",
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

        $results = DB::table('cat_services');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_services",
     *     tags={"cat_services"},
     *     summary="Get all cat services",
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
        $services =  CatService::all();
        return response($services, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_services/{id}",
     *     tags={"cat_services"},
     *     summary="Get cat services",
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
        $service = CatService::find($id);
        return response($service, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/cat_services",
     *     tags={"cat_services"},
     *     summary="Get cat services",
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
		$service = new CatService;
		$service->name = $request->get('name');
        $service->price = $request->get('price');
		$service->save();
		return response()->json(['success' => true]);
	}

    /**
     * @OA\Put(
     *     path="/api/cat_services/{id}",
     *     tags={"cat_services"},
     *     summary="Update cat services",
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
    	$service = CatService::find($id);
		$service->name = $request->get('name');
        $service->price = $request->get('price');
		$service->save();
    	return response()->json(['success' => true]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cat_services/{id}",
     *     tags={"cat_services"},
     *     summary="Delete cat services",
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
        $service = CatService::find($id);
		$service->delete();
    	return response()->json(['success' => true]);
    }
}

