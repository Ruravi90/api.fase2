<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageTracking;
class PackageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/packages/{id}",
     *     tags={"packages"},
     *     summary="Get packages per paginate",
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
		if($request->has('perPage')){
			$perPage = $request->get('perPage');
        }

        $packages = Package::with(['client','type','sale','tracking','tracking.user'])
        ->where('is_completed',$request->get('isCompleted'));

        $packages = $packages->orderBy('updated_at', 'desc');
        $packages = $packages->paginate($perPage);

		return response($packages, 200)->header('Content-Type', 'application/json');
	}

	public function add(Request $request){

		return ['success',true];
	}

    public function update($id,Request $request){

    	return ['success',true];
    }


    /**
     * @OA\Delete(
     *     path="/api/packages/{id}",
     *     tags={"packages"},
     *     summary="Delte packages",
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
    	$package = Package::find($id);
		$package->delete();
    	return ['success',true];
    }

    /**
     * @OA\Get(
     *     path="/api/packages",
     *     tags={"packages"},
     *     summary="Get all packages",
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
        $packages = Package::with(['client','type','sale','tracking','tracking.user'])->get();
        return response($packages, 200)->header('Content-Type', 'application/json');
    }


    /**
     * @OA\Get(
     *     path="/api/packages/{id}",
     *     tags={"packages"},
     *     summary="Get packages",
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
        $package = Package::with(['client','type','sale','tracking','tracking.user'])->find($id);
        return response($package, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/packages/is_completed/{id}",
     *     tags={"packages"},
     *     summary="Package is completed",
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
    public function isCompleted(Request $request){
        $package = Package::with(['client','type','sale','tracking','tracking.user'])
        ->where('is_completed',$request->get('isCompleted'))
        ->get();
        return response($package, 200)->header('Content-Type', 'application/json');
    }
}
