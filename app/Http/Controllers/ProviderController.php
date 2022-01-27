<?php
namespace fase2\Http\Controllers;
use fase2\Provider;
use fase2\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use fase2\Http\Requests\ProviderRequest;

/**
 * Provider controller
 *
*/
class ProviderController extends Controller
{
	/**
	 * @Return view
	 *
	*/
	public function index()
	{
		return view('provider.index');
	}

    /**
	 * Client controller
	 *
	*/
	public function getAll(){
		$providers =  Provider::all();
		return response($providers, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * Client controller
	 *
	*/
	public function find($id){
		$provider = Provider::with('address')->find($id);
		return response($provider, 200)->header('Content-Type', 'application/json');
	}
	/**
	 * Client controller 
	 *
	*/
	public function add(ProviderRequest $request){
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
	 * Client controller
	 *
	*/
    public function update($id,ProviderRequest $request){// se envia el id a $client 
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
	 * Client controller
	 *
	*/
    public function delete($id){// se envia el id a $client 
    	$provider = Provider::find($id);
    	$provider->delete();
    	return response(['success' => true],200)->header('Content-Type', 'application/json');
    }
}