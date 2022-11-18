<?php
namespace App\Http\Controllers;
use App\Models\Provider;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ProviderRequest;

/**
 * Provider controller
 *
*/
class ProviderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/providers",
     *     tags={"provides"},
     *     summary="Get all providers",
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
		$providers =  Provider::all();
		return response($providers, 200)->header('Content-Type', 'application/json');
	}
	/**
     * @OA\Get(
     *     path="/api/providers/{id}",
     *     tags={"provides"},
     *     summary="Get provider",
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
		$provider = Provider::with('address')->find($id);
		return response($provider, 200)->header('Content-Type', 'application/json');
	}
	/**
     * @OA\Post(
     *     path="/api/providers",
     *     tags={"provides"},
     *     summary="Add provider",
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
		$provider = new Provider;
		$provider->business_name = $request->get('business_name');
		$provider->contact_name = $request->get('contact_name');
		$provider->office_phone = $request->get('office_phone');
		$provider->email = $request->get('email');
		$provider->save();

		if($request->get('street') != null){
			$address = new Address;
			$address->name = $request->get('street');
			$address->inner_number = $request->get('inner_number');
			$address->outdoor_number = $request->get('outdoor_umber');
			$address->postal_code = $request->get('postal_code');
			$address->town = $request->get('town');
			$address->state = $request->get('state');
			$address->provider_id = $provider->id;
			$address->save();
		}

		return response($provider, 200)->header('Content-Type', 'application/json');
	}
	/**
     * @OA\Put(
     *     path="/api/providers/{id}",
     *     tags={"provides"},
     *     summary="Update provider",
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
    	$provider = Provider::find($id);
    	$provider->business_name = $request->get('business_name');
    	$provider->contact_name = $request->get('contact_name');
    	$provider->office_phone = $request->get('office_phone');
    	$provider->email = $request->get('email');
    	$provider->save();
    	$address = $provider->address()->first();

    	if($address == null && $request->has('street')){
    		$address = new Address;
    		$address->name = $request->get('street');
    		$address->inner_number = $request->get('inner_number');
    		$address->outdoor_number = $request->get('outdoor_umber');
    		$address->postal_code = $request->get('postal_code');
    		$address->town = $request->get('town');
    		$address->state = $request->get('state');
    		$address->provider_id = $provider->id;
    		$address->save();
    	}

    	else if($address != null){
    		$address->name = $request->get('street');
    		$address->inner_number = $request->get('inner_number');
    		$address->outdoor_number = $request->get('outdoor_umber');
    		$address->postal_code = $request->get('postal_code');
    		$address->town = $request->get('town');
    		$address->state = $request->get('state');
    		$address->save();
    	}

		//$client = Clien::update($request->only('name','last_name','mother_last_name'));
    	return response($provider, 200)->header('Content-Type', 'application/json');
    }
	/**
     * @OA\Delete(
     *     path="/api/providers/{id}",
     *     tags={"provides"},
     *     summary="Delete provider",
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
    	$provider = Provider::find($id);
    	$provider->delete();
    	return response(['success' => true],200)->header('Content-Type', 'application/json');
    }
}
