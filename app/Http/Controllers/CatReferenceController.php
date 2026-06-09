<?php
namespace App\Http\Controllers;
use App\Models\CatReference;
use Illuminate\Http\Request;
use App\Http\Requests\CatReferenceRequest;
use Illuminate\Support\Facades\DB;

class CatReferenceController extends Controller
{
    //
    public function index(){
    	$references = CatReference::orderBy('id','desc')->paginate(10);
    	return view('cat_reference.index')->with(['references'=> $references]);
    }

    // Api rest
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }
        
        $results = DB::table('cat_references');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%'); 

        $results = $results->orderBy('name', 'asc')->paginate($perPage);
        
		return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){
        $references =  CatReference::all();
        return response($references, 200)
        ->header('Content-Type', 'application/json');
    }

    public function find($id){
        $reference = CatReference::find($id);
        return response($reference, 200)
        ->header('Content-Type', 'application/json');
    }

	public function add(CatReferenceRequest $request){ 
		CatReference::create(
            $request->only('name')
        );
		return response('success', 200)
        ->header('Content-Type', 'application/json');
	}

    public function update($id,CatReferenceRequest $request){// se envia el id a $client 
    	$reference = CatReference::find($id);
		$reference->update(
            $request->only('name')
        );
    	return response('success', 200)
        ->header('Content-Type', 'application/json');
    }

    public function delete($id){// se envia el id a $client 
        $reference = CatReference::find($id);
		$reference->delete();
    	return response('success', 200)
        ->header('Content-Type', 'application/json');
    }
}

