<?php
namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Address;
use App\Models\CatReference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ClientRequest;

class ClientController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     tags={"clients"},
     *     summary="Get all clients",
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
		return response(Client::all(), 200)
		->header('Content-Type', 'application/json');
    }

	/**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     tags={"clients"},
     *     summary="Get client",
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
        $client = Client::with('address','reference')->find($id);
		return response($client, 200)
		->header('Content-Type', 'application/json');
    }

	/**
     * @OA\Post(
     *     path="/api/clients",
     *     tags={"clients"},
     *     summary="Add client",
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
		if(intval($request->get('reference_id')) == -1){
			$reference = new CatReference;
			$reference->name = $request->get('other_ref');
			$reference->save();
		}
		else
			$reference = CatReference::find($request->get('reference_id'));

		$clientsCounts = Client::whereRaw('LOWER(name) = ?',trim(strtolower($request->get('name'))))
		->whereRaw('LOWER(lastname) = ?', trim(strtolower($request->get('lastname'))))
		->whereRaw('LOWER(motherlastname) = ?', trim(strtolower($request->get('motherlastname'))))
		->count();

		if($clientsCounts > 0){
			return response('El cliente ya existe',400)->header('Content-Type', 'application/json');
		}


		$client = new Client;
		$client->name = $request->get('name');
		$client->lastname = $request->get('lastname');
		$client->motherlastname = $request->get('motherlastname');
		$client->email = $request->get('email');

		$client->phone_home = $request->get('phone_home');
		$client->phone_mobile = $request->get('phone_mobile');
		$client->reference_id = $reference->id;
		$client->save();

		if($request->has('address')){
			foreach ($request->get('address') as $_address) {
				$address = new Address;
				$address->suburb = $_address['suburb'];
				$address->name = "NA";
				$address->inner_number = 0;
				//$address->outdoor_number = 0;
				//$address->postal_code = 0;
				//$address->town =  "NA";
				//$address->state =  "NA";
				$address->client_id = $client->id;
				$address->save();
			}
		}
		////auth()->user->id obtener usuario legueado
		////\App::user->id
		////$request->user->id
		////$client = Clien::create($request->only('name','last_name','mother_last_name'));
		return response($client, 200)
		->header('Content-Type', 'application/json');
	}

	/**
     * @OA\Post(
     *     path="/api/clients/paginate",
     *     tags={"clients"},
     *     summary="Get clients per paginate",
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

		$from = date('Y-m-d' . ' 00:00:00', time());
		$clients = Client::with('address','reference');

		if($request->has('shared') && $request->get('shared') != ''){
			$clients = $clients->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('shared')) . '%')
			->orWhereRaw('LOWER(lastName) like ?', '%' . strtolower($request->get('shared')) . '%');
		}

		$clients = $clients->orderBy('name', 'asc')->paginate($perPage);
		//->where('created_at','>=',$from)->paginate($perPage);

		//if(count($clients) == 0)
		//	return Response::make(['message' => 'No se encontraron registros'], 404);

		return response($clients, 200)->header('Content-Type', 'application/json');
	}

	/**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     tags={"clients"},
     *     summary="Update client",
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
    	//dd($client); //saber que contiene la variable
    	if(intval($request->get('reference_id')) == -1){
			$reference = new CatReference;
			$reference->name = $request->get('other_ref');
			$reference->save();
		}
		else
			$reference = CatReference::find($request->get('reference_id'));

    	$client = Client::find($id);
		$client->name = $request->get('name');
		$client->lastname = $request->get('lastname');
		$client->motherlastname = $request->get('motherlastname');
		$client->email = $request->get('email');
		$client->phone_home = $request->get('phone_home');
		$client->phone_mobile = $request->get('phone_mobile');
		$client->reference_id = $reference->id;
		$client->save();

		$address = $client->address()->first();
		if($address == null && $request->has('address')){
			foreach ($request->get('address') as $_address) {
				$address = new Address;
				$address->suburb = $_address['suburb'];
				$address->name = "NA";
				$address->inner_number = 0;
				//$address->outdoor_number = 0;
				//$address->postal_code = 0;
				//$address->town =  "NA";
				//$address->state =  "NA";
				$address->client_id = $client->id;
				$address->save();
			}
		}
		else if($address != null && $request->has('address')){
			foreach ($request->get('address') as $_address) {
				$address = new Address;
				$address->suburb = $_address['suburb'];
				$address->name = "NA";
				$address->inner_number = 0;
				//$address->outdoor_number = 0;
				//$address->postal_code = 0;
				//$address->town =  "NA";
				//$address->state =  "NA";
				$address->client_id = $client->id;
				$address->save();
			}
		}

		//$client = Clien::update($request->only('name','last_name','mother_last_name'));
    	return response()->json(['success' => true]);
    }

	/**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     tags={"clients"},
     *     summary="Delete client",
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
    	$client = Client::find($id);
		$client->delete();
    	return response()->json(['success' => true]);
    }

}
