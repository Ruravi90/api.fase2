<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\CatPackage;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\PackageTracking;

class PackageTrackingController extends Controller
{
    public function index()
	{
		return view('package.index');
	}

    public function add(Request $request){
        $track = new PackageTracking;
        $track->user_id = $request->get('user_id');
        $track->package_id = $request->get('package_id');
        $track->is_taken = $request->get('is_taken');
        $track->description = $request->get('description');
        $track->scheduled_date = $request->get('scheduled_date');
        $track->save();

        $package = Package::with('type')->find($request->get('package_id'));
        $tracksCount = PackageTracking::where('package_id',$request->get('package_id'))->count();

        if($package->type->session_count == $tracksCount && $package->is_paid == 0){
            $package->is_completed = true;
            $package->save();
        }
 
        $catPackage = CatPackage::with(['complements'])
                ->find($package->type->id);
                
        foreach ($catPackage->complements as $_complement){
            if($_complement["pill_id"] != null){ 
                $pillInventory = PillInventory::where('pill_id',$_complement["pill_id"])->first();
                $pillInventory->count = ((int)$pillInventory->count - (int)$_complement["count"]);
                $pillInventory->save();
            }
            if($_complement["product_id"] != null){
                $productInventory = ProductInventory::where('product_id',$_complement["product_id"])->first();
                $productInventory->count = ((int)$productInventory->count - (int)$_complement["count"]);
                $productInventory->save();
            }
        }

        return response(['Track' =>  $track, 'CatPackage' => $catPackage],200)
        ->header('Content-Type', 'application/json');
    }

    public function update($id,Request $request){
        $track = PackageTracking::find($id);
        $track->user_id = $request->get('user_id');
        $track->package_id = $request->get('package_id');
        $track->is_taken = $request->get('is_taken');
        $track->description = $request->get('description');
        $track->scheduled_date = $request->get('scheduled_date');
        $track->save();

        return response($track, 200)->header('Content-Type', 'application/json');
    }

	public function delete($id){
    	$track = PackageTracking::find($id);
		$track->delete();
    	return response("Ok", 200)->header('Content-Type', 'application/json');
    }

    public function getAll(){
        $tracking = PackageTracking::all();
        return response($tracking, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $track = PackageTracking::with(['package','user'])->find($id);
        return response($track, 200)->header('Content-Type', 'application/json');
    }

    public function forPackageId($id){
        $track = PackageTracking::with(['user'])->where('package_id','=',$id)->get();
        return response($track, 200)->header('Content-Type', 'application/json');
    }
}
