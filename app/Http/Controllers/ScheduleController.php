<?php
namespace App\Http\Controllers;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ScheduleController extends Controller
{


/**
     * @OA\Post(
     *     path="/api/schedules",
     *     tags={"schedules"},
     *     summary="Add schedules",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
	public function add(Request $request){
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
/**
     * @OA\Put(
     *     path="/api/schedules/{id}",
     *     tags={"schedules"},
     *     summary="Update schedules",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function update($id,Request $request){// se envia el id a $client
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
/**
     * @OA\Delete(
     *     path="/api/schedules/{id}",
     *     tags={"schedules"},
     *     summary="Delete schedules",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function delete($id){
    	$schedule = Schedule::find($id);
		$schedule->delete();
    	return ['success' => true];
    }
/**
     * @OA\Get(
     *     path="/api/schedules",
     *     tags={"schedules"},
     *     summary="Get all schedules",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function getAll(){
        $schedule =  Schedule::all();
        return response($schedule, 200)->header('Content-Type', 'application/json');
    }
/**
     * @OA\Get(
     *     path="/api/schedules/{id}",
     *     tags={"schedules"},
     *     summary="Get schedules",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function find($id){
        $schedule = Schedule::with('client')->find($id);
        return response($schedule, 200)->header('Content-Type', 'application/json');
    }
}
