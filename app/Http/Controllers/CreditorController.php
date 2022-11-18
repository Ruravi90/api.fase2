<?php
namespace App\Http\Controllers;
use App\Models\Creditor;
use App\Models\Address;
use App\Http\Requests\CreditorRequest;
use Illuminate\Http\Request;

class CreditorController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/creditos",
     *     tags={"creditors"},
     *     summary="Get all creditos",
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
        $creditors =  Creditor::all();
        return response($creditors, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/creditos/{id}",
     *     tags={"creditors"},
     *     summary="Get credito",
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
        $creditor = Creditor::with('address')->find($id);
        return response($creditor, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/creditos/{id}",
     *     tags={"creditors"},
     *     summary="Add credito",
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
		$creditor = new Creditor;
		$creditor->business_name = $request->get('business_name');
		$creditor->contact_name = $request->get('contact_name');
		$creditor->office_phone = $request->get('office_phone');
		//$creditor->email = $request->get('email');
		$creditor->save();

		if($request->get('street') != null){
			$address = new Address;
			$address->name = $request->get('street');
			$address->inner_number = $request->get('inner_number');
			$address->outdoor_number = $request->get('outdoor_umber');
			$address->postal_code = $request->get('postal_code');
			$address->town = $request->get('town');
			$address->state = $request->get('state');
			$address->creditor_id = $creditor->id;
			$address->save();
		}
		//auth()->user->id obtener usuario legueado
		//\App::user->id
		////$request->user->id
		//$client = Clien::create($request->only('name','last_name','mother_last_name'));
		return response(['success' => true],200)->header('Content-Type', 'application/json');
	}

    /**
     * @OA\Put(
     *     path="/api/creditos/{id}",
     *     tags={"creditors"},
     *     summary="Update credito",
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
    	$creditor = Creditor::find($id);
		$creditor->business_name = $request->get('business_name');
		$creditor->contact_name = $request->get('contact_name');
		$creditor->office_phone = $request->get('office_phone');
		//$creditor->email = $request->get('email');
		$creditor->save();

		$address = $creditor->address()->first();

		if($address == null && $request->has('street')){
			$address = new Address;
			$address->name = $request->get('street');
			$address->inner_number = $request->get('inner_number');
			$address->outdoor_number = $request->get('outdoor_umber');
			$address->postal_code = $request->get('postal_code');
			$address->town = $request->get('town');
			$address->state = $request->get('state');
			$address->creditor_id = $creditor->id;
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

    	return response(['success' => true],200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Delete(
     *     path="/api/creditos/{id}",
     *     tags={"creditors"},
     *     summary="Delete credito",
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
    	$creditor = Creditor::find($id);
		$creditor->delete();
    	return response(['success' => true],200)->header('Content-Type', 'application/json');
    }
}
