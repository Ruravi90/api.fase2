<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageTracking;
class PackageController extends Controller
{
    public function index()
	{
		return view('package.index');
    }
    
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('perPage')){
			$perPage = $request->get('perPage');
        }
        
        $packages = Package::with(['client','type','sale','tracking','tracking.user'])
        ->where('is_completed',$request->get('isCompleted'));

        if($request->filled('search')) {
            $search = $request->get('search');
            $packages = $packages->whereHas('client', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('lastname', 'LIKE', "%{$search}%")
                  ->orWhere('motherlastname', 'LIKE', "%{$search}%");
            });
        }

        $packages = $packages->orderBy('updated_at', 'desc');
        $packages = $packages->paginate($perPage);

		return response($packages, 200)->header('Content-Type', 'application/json');
	}

	public function add(Request $request){

		return ['success',true];
	}

    public function update($id,Request $request){

    	return ['success',true];
    }

	public function delete($id){
    	$package = Package::find($id);
		$package->delete();
    	return ['success',true];
    }

    public function getAll(){
        $packages = Package::with(['client','type','sale','tracking','tracking.user'])->get();
        return response($packages, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $package = Package::with(['client','type','sale','tracking','tracking.user'])->find($id);
        return response($package, 200)->header('Content-Type', 'application/json');
    }

    public function isCompleted(Request $request){
        $package = Package::with(['client','type','sale','tracking','tracking.user'])
        ->where('is_completed',$request->get('isCompleted'))
        ->get();
        return response($package, 200)->header('Content-Type', 'application/json');
    }
}
