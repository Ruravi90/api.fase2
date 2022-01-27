<?php

namespace fase2\Http\Controllers;

use Illuminate\Http\Request;
use fase2\SaleAdditional;

class SaleAdditionalController extends Controller
{
    // Api rest
	public function getAll(){
		$additionals = SaleAdditional::with(['sale','cat_pill','cat_product'])->get();
		return response($additionals, 200)->header('Content-Type', 'application/json');
	}

	public function find($id){
		$additional = SaleAdditional::with(['sale','cat_pill','cat_product'])->find($id);
		return response($additional, 200)->header('Content-Type', 'application/json');
    }

    public function add(Request $request){
        $additional = SaleAdditional::find($id);
        $additional->sale_id = $request->get('sale_id');
        $additional->pill_id = $request->get('pill_id');
        $additional->product_id = $request->get('product_id');
        $additional->count = $request->get('count');
        $additional->save();
    }
    
    public function update($id,Request $request){// se envia el id a $client 
        $additional = SaleAdditional::find($id);
        $additional->pill_id = $request->get('pill_id');
        $additional->product_id = $request->get('product_id');
        $additional->count = $request->get('count');
        $additional->save();
    	return response($provider, 200)->header('Content-Type', 'application/json');
    }

    public function delete($id){// se envia el id a $client 
    	$provider = SaleAdditional::find($id);
    	$provider->delete();
    	return response("Ok", 200)->header('Content-Type', 'application/json');
    }
}
