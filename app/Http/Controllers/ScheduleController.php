<?php
namespace fase2\Http\Controllers;
use fase2\Client;
use fase2\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use fase2\Http\Requests\ScheduleRequest;


class ScheduleController extends Controller
{
    public function index(){
    	return view('home');
    }

    // Api rest
	public function add(ScheduleRequest $request){
		$schedule = new Schedule;
		$schedule->title = $request->get('title');
		$schedule->description = $request->get('description');
		$schedule->start = $request->get('start');
		$schedule->end = $request->get('end');
		$schedule->color = $request->get('color');
		$schedule->allDay = $request->get('allDay');
		$schedule->client_id = $request->get('client_id');
		$schedule->save();
		return ['success' => true]; 
	}

    public function update($id,ScheduleRequest $request){// se envia el id a $client 
    	$schedule = Schedule::find($id);
		$schedule->title = $request->get('title');
		$schedule->description = $request->get('description');
		$schedule->start = $request->get('start');
		$schedule->end = $request->get('end');
		$schedule->color = $request->get('color');
		$schedule->allDay = $request->get('allDay'); 
		$schedule->client_id = $request->get('client_id');
		$schedule->save();

    	return ['success' => true]; 
    }

    public function delete($id){
    	$schedule = Schedule::find($id);
		$schedule->delete();
    	return ['success' => true]; 
    }

    public function getAll(){
        $schedule =  Schedule::all();
        return response($schedule, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $schedule = Schedule::with('client')->find($id);
        return response($schedule, 200)->header('Content-Type', 'application/json');
    }
}