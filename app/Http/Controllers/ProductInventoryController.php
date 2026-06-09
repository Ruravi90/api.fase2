<?php

namespace App\Http\Controllers;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{
    public function index()
	{
		return view('products_inventory.index');
	}

	// Api rest
	public function add(Request $request){
        $inventory = new ProductInventory;
		$inventory->product_id = $request->get('product_id');
		$inventory->count = $request->get('count');
		$inventory->save();
		return ['success' => true]; 
	}

    public function update($id,Request $request){// se envia el id a $client 
		$inventory = ProductInventory::find($id);
		$inventory->product_id = $request->get('product_id');
		$inventory->count = $request->get('count');
		$inventory->save();
    	return ['success' => true]; 
    }

    public function delete($id){
    	$user = ProductInventory::find($id);
		$user->delete();
    	return ['success' => true]; 
    }

    public function getAll(){
        $inventory = ProductInventory::with('product')->get();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $inventory = ProductInventory::with('product')->find($id);
        return response($inventory, 200)->header('Content-Type', 'application/json');
	}
	
	public function forProduct($id){
        $inventory = ProductInventory::with('product')->where('product_id',$id)->first();
        return response($inventory, 200)->header('Content-Type', 'application/json');
    }

}
