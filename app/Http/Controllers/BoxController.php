<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Department;
use App\Models\CatExpense;
use App\Models\HistoryBalance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class BoxController extends Controller
{
	public function index(){
		return view('box.index');
	}

	public function getBalance(Request $request){
		$dt = new DateTime();
		if($request->has("date")){
			$dt = new DateTime($request->get("date"));
		}
		$start = Carbon::instance($dt)->startOfMonth();
		$end = Carbon::instance($dt)->endOfMonth();

		$departaments = Department::all();
		$json = array();
		$count = 0;

		foreach ($departaments as $_departmanet){
			$catExpenses = CatExpense::all();

			$json[$count]['dateStart'] = $start->toDateTimeString();
			$json[$count]['dateEnd'] = $end->toDateTimeString();

			$json[$count]['name'] = $_departmanet->name;

			$subCount = 0;
			$expenses = array();
			foreach ($catExpenses as $catExpense){
				$expenses[$subCount]['name'] = $catExpense->name;

				$expenseTotal = Purchase::where('department_id',$_departmanet->id)
				->where('expence_id',$catExpense->id)
				->where('is_paid',1)
				->where('updated_at','>=', $start->toDateTimeString())
				->where('updated_at','<=', $end->toDateTimeString())
				->sum('amount');

				$expenses[$subCount]['total']  = intval($expenseTotal);

				$subCount++;
			}

			$json[$count]['expenses'] = $expenses;

			$purchaseTotal = Purchase::where('department_id',$_departmanet->id)
			->where('is_paid',1)
			->where('updated_at','>=', $start->toDateTimeString())
			->where('updated_at','<=', $end->toDateTimeString())
			->sum('amount');

			$salesTotal = Sale::where('department_id',$_departmanet->id)
			->where('is_paid',1)
			->where('primary_id','<>', null)
			->where('updated_at','>=', $start->toDateTimeString())
			->where('updated_at','<=', $end->toDateTimeString())
			->sum('amount');

			$history = HistoryBalance::where('updated_at','<',$end->toDateTimeString())
			->where('department_id',$_departmanet->id)
			->orderBy('id', 'DESC')
			->first();

			if($history != null && intval($history->utility) > 0){
				$salesTotal = (intval($salesTotal) + intval($history->utility));
			}

			$json[$count]['purchaseTotal'] = intval($purchaseTotal);
			$json[$count]['salesTotal'] = intval($salesTotal);
			$json[$count]['total'] = ($salesTotal - $purchaseTotal);

			$count++;
		}

		return response()->json($json);
	}

	public function getSalesChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::where('primary_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));

				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));

				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('date')->get();

    	return response()->json($json);
	}

	public function getSalesForPackageChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::with('cat_package')
		->where('primary_id','<>', null)
		->where('package_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales,package_id'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));
				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales,package_id'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));

				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('package_id','date')->get();

    	return response()->json($json);
	}

	public function getSalesForServiceChart(Request $request){
		$isPaid = true;
		if($request->has('isPaid'))
			$isPaid = $request->get('isPaid');

		$json = Sale::with('cat_service')
		->where('primary_id','<>', null)
		->where('service_id','<>', null)
		->where('is_paid', $isPaid);

		switch($request->get('time')){
			case'y':
				$json = $json->select(DB::raw('count(id) as sales,service_id'), DB::raw("DATE_FORMAT(created_at, '%Y') as date"));
				break;
			default;
				$json = $json->select(DB::raw('count(id) as sales,service_id'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') as date"));

				$nowYear = date('Y-m-d' . ' 00:00:00');
				$oldYear = date('Y-m-d' . ' 00:00:00',strtotime('-1 years'));
				$json = $json->whereBetween('created_at', array($oldYear , $nowYear));
				break;
		}

		$json = $json->groupby('service_id','date')->get();

    	return response()->json($json);
    }
}
