<?php
namespace App\Http\Controllers;
use App\Models\CatProduct;
use Illuminate\Http\Request;
use App\Http\Requests\CatProductRequest;
use Illuminate\Support\Facades\DB;

class CatProductController extends Controller
{
    //
    public function index(){
    	return view('cat_product.index');
    }

    // Api rest
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }
        
        $results = DB::table('cat_products');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%'); 

        $results = $results->orderBy('name', 'asc')->paginate($perPage);
        
		return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){
        $products =  CatProduct::with(['inventory'])->get();
        return response($products, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $product = CatProduct::find($id);
        return response($product, 200)->header('Content-Type', 'application/json');
    }

	public function add(CatProductRequest $request){
        $product =new CatProduct;
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->save();
        return ['success' => true]; 
	}

    public function update($id,CatProductRequest $request){// se envia el id a $client 
    	$product = CatProduct::find($id);
		$product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->save();
    	return ['success' => true]; 
    }

    public function delete($id){// se envia el id a $client 
        $product = CatProduct::find($id);
		$product->delete();
    	return ['success' => true]; 
    }
}

