<?php
namespace fase2\Http\Controllers;
use fase2\CatService;
use Illuminate\Http\Request;
use fase2\Http\Requests\CatServiceRequest;
use Illuminate\Support\Facades\DB;

class CatServiceController extends Controller
{
    //
    public function index(){
    	return view('cat_service.index'); 
    }

    // Api rest
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }
        
        $results = DB::table('cat_services');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%'); 

        $results = $results->orderBy('name', 'asc')->paginate($perPage);
        
		return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){
        $services =  CatService::all();
        return response($services, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $service = CatService::find($id);
        return response($service, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){
		$service = new CatService;
		$service->name = $request->get('name');
        $service->price = $request->get('price');
		$service->save();
		return response()->json(['success' => true]);
	}

    public function update($id,Request $request){// se envia el id a $client 
    	$service = CatService::find($id);
		$service->name = $request->get('name');
        $service->price = $request->get('price');
		$service->save();
    	return response()->json(['success' => true]);
    }

    public function delete($id){// se envia el id a $client 
        $service = CatService::find($id);
		$service->delete();
    	return response()->json(['success' => true]);
    }
}

