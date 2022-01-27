<?php
namespace fase2\Http\Controllers;
use Illuminate\Http\Request;
use fase2\PillInventory;

class PillInventoryController extends Controller
{
    public function index()
	{
		return view('pills_inventory.index');
	}

	public function getAll(){ 
        $inventory =  PillInventory::with('pill')->get();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $inventory = PillInventory::with('pill')->find($id);
        return response($inventory, 200)->header('Content-Type', 'application/json');
	}
	
	public function forPill($id){
        $inventory = PillInventory::with('pill')->where('pill_id',$id)->first();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){
        $inventory = new PillInventory;
    	$inventory->pill_id = $request->get('pill_id');
		$inventory->count = $request->get('count');
		$inventory->save();
        return ['success' => true]; 
	}

    public function update($id,Request $request){// se envia el id a $client 
    	$inventory = PillInventory::find($id);
		$inventory->pill_id = $request->get('pill_id');
		$inventory->count = $request->get('count');
		$inventory->save();
    	return ['success' => true]; 
    }

    public function delete($id){// se envia el id a $client 
        $inventory = PillInventory::find($id);
		$inventory->delete();
    	return ['success' => true]; 
    }
}
