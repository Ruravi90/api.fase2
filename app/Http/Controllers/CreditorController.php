<?php
namespace App\Http\Controllers;
use App\Models\Creditor;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\CreditorRequest;

class CreditorController extends Controller
{

    public function index()
    {
        return view('creditor.index');
    }

    // Api rest
    public function getAll(){
        $creditors =  Creditor::all();
        return response($creditors, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $creditor = Creditor::with('address')->find($id);
        return response($creditor, 200)->header('Content-Type', 'application/json');
    }

	public function add(CreditorRequest $request){
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

    public function update($id,CreditorRequest $request){// se envia el id a $client 
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

    public function delete($id){// se envia el id a $client 
    	$creditor = Creditor::find($id);
		$creditor->delete();
    	return response(['success' => true],200)->header('Content-Type', 'application/json');
    } 
}