<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatTypeSale;
use Illuminate\Support\Facades\DB;

class CatTypeSalesController extends Controller
{
     //
    public function index(){
    	$type = CatTypeSale::orderBy('id','desc')->paginate(10);
    	return view('cat_type_sales.index')->with(['type_sales'=> $type]);
    }

    // Api rest
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }
        
        $results = DB::table('cat_type_sale');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%'); 

        $results = $results->orderBy('name', 'asc')->paginate($perPage);
        
		return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){
        $type =  CatTypeSale::all();
        return response($type, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $type = CatTypeSale::find($id);
        return response($type, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){ 
		CatTypeSale::create(
            $request->only('name')
        );
		return ['success' => true]; 
	}

    public function update($id,Request $request){// se envia el id a $client 
    	$type = CatTypeSale::find($id);
		$type->update(
            $request->only('name')
        );
    	return ['success' => true]; 
    }

    public function delete($id){// se envia el id a $client 
        $type = CatTypeSale::find($id);
		$type->delete();
    	return ['success' => true]; 
    }
}
