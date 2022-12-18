<?php
namespace App\Http\Controllers;
use App\Models\CatPackage;
use App\Models\PackageComplement;
use Illuminate\Http\Request;
use App\Http\Requests\CatPackageRequest;
use Illuminate\Support\Facades\DB;

class CatPackageController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/cat_packages/paginate",
     *     tags={"cat_packages"},
     *     summary="Get cat_packages per paginate",
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

        $results = DB::table('cat_packages');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

        $results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_packages",
     *     tags={"cat_packages"},
     *     summary="Get all cat_packages",
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
        $packages =  CatPackage::with(['complements'])->get();
        return response($packages, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/cat_packages/{id}",
     *     tags={"cat_packages"},
     *     summary="Get cat_packages",
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
        $package = CatPackage::with(['complements','complements.cat_product'])->find($id);
        return response($package, 200)->header('Content-Type', 'application/json');
    }
/**
     * @OA\Post(
     *     path="/api/cat_packages/{id}",
     *     tags={"cat_packages"},
     *     summary="Add cat_packages",
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
        $package = new CatPackage;
        $package->name = $request->get('name');
        $package->price = $request->get('price');
        $package->session_count = $request->get('session_count');
        $package->save();

        if ($request->has('complements')) {
            foreach ($request->get('complements') as $_complement){
                $complement = new PackageComplement;
                $complement->package_id = $package->id;
                $complement->count = $_complement['count'];

                if(isset($_complement['product_id']))
                    $complement->product_id = $_complement['product_id'];

                $complement->save();
            }
        }

		return response($package, 200)->header('Content-Type', 'application/json');
	}
/**
     * @OA\Put(
     *     path="/api/cat_packages/{id}",
     *     tags={"cat_packages"},
     *     summary="Update cat_packages",
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
    	$package = CatPackage::with(['complements'])->find($id);
		$package->name = $request->get('name');
        $package->price = $request->get('price');
        $package->session_count = $request->get('session_count');
        $package->save();

        if($package->complements->isNotEmpty()){
            $complements = PackageComplement::where('package_id','=',$package->id);
            $complements->delete();
        }

        foreach ($request->get('complements') as $_complement){
            $complement = new PackageComplement;
            $complement->package_id = $package->id;
            $complement->count = $_complement['count'];

            if(isset($_complement['product_id']))
                $complement->product_id = $_complement['product_id'];

            $complement->save();
        }

    	return response($package, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Delete(
     *     path="/api/cat_packages/{id}",
     *     tags={"cat_packages"},
     *     summary="Delete cat_packages",
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
        $package = CatPackage::find($id);
		$package->delete();
        return response()->json(['success' => true]);
    }
}

