<?php
namespace fase2\Http\Controllers;
use Illuminate\Http\Request;
use fase2\CatPill;
use Illuminate\Support\Facades\DB;

class CatPillController extends Controller
{
    public function index(){
    	return view('cat_pill.index');
    }

    // Api rest
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }
        
        $results = DB::table('cat_pills');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%'); 

        $results = $results->orderBy('name', 'asc')->paginate($perPage);
        
		return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){ 
        $products =  CatPill::with(['inventory'])->get();
        return response($products, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $product = CatPill::find($id);
        return response($product, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){
        $pill =new CatPill;
        $pill->name = $request->get('name');
        $pill->price = $request->get('price');
        $pill->save();
        return ['success' => true]; 
	}

    public function update($id,Request $request){// se envia el id a $client 
    	$pill = CatPill::find($id);
		$pill->name = $request->get('name');
        $pill->price = $request->get('price');
        $pill->save();
    	return ['success' => true]; 
    }

    public function delete($id){// se envia el id a $client 
        $product = CatPill::find($id);
		$product->delete();
    	return ['success' => true]; 
    }
}
