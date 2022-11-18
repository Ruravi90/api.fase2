<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatConcept;
use Illuminate\Support\Facades\DB;

class CatConceptController extends Controller
{

    public function getPaginate(Request $request) {

		$perPage = 15;
		if($request->has('per_page')){
			$perPage = $request->get('per_page');
        }

        $results = DB::table('cat_concepts');
        if($request->has('name'))
		    $results = $results->whereRaw('LOWER(name) like ?', '%' . strtolower($request->get('name')) . '%');

		$results = $results->orderBy('name', 'asc')->paginate($perPage);

		return response($results, 200)->header('Content-Type', 'application/json');
	}

    public function getAll(){
        $expences = CatConcept::all();
        return response($expences, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $expence = CatConcept::find($id);
        return response($expence, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){
        $expence = new CatConcept;
        $expence->name = $request->get('name');
        $expence->save();

		return response($expence, 200)->header('Content-Type', 'application/json');
	}

    public function update($id,Request $request){// se envia el id a $client
    	$expence = CatConcept::find($id);
		$expence->name = $request->get('name');
        $expence->save();
    	return response($expence, 200)->header('Content-Type', 'application/json');
    }

    public function delete($id){// se envia el id a $client
        $expence = CatConcept::find($id);
		$expence->delete();
    	return response('Ok', 200)->header('Content-Type', 'application/json');
    }
}
