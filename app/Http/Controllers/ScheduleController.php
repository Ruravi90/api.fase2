<?php
namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\Package;
use App\Models\CatPackage;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\PackageTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ScheduleRequest;
use App\Services\OpenWAService;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected $whatsapp;

    public function __construct(OpenWAService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index(){
    	return view('home');
    }

    // Api rest
	public function add(ScheduleRequest $request){
        $start = $request->get('start');
        $end = $request->get('end');

        $overlapping = Schedule::where(function($query) use ($start, $end) {
            $query->where('start', '<', $end)
                  ->where('end', '>', $start);
        })->exists();

        if ($overlapping) {
            return response(['success' => false, 'error' => 'El horario se empalma con otra cita existente.'], 400);
        }

		$schedule = new Schedule;
		$schedule->title = $request->get('title');
		$schedule->description = $request->get('description');
		$schedule->start = $start;
		$schedule->end = $end;
		$schedule->color = $request->get('color');
		$schedule->allDay = $request->get('allDay');
		$schedule->client_id = $request->get('client_id');
        $schedule->package_id = $request->get('package_id');
        if ($request->has('service_id')) {
            $schedule->service_id = $request->get('service_id');
        }
		$schedule->save();

        if ($schedule->client_id) {
            $client = Client::find($schedule->client_id);
            if ($client && $client->phone) {
                $date = Carbon::parse($schedule->start)->locale('es');
                $fecha = ucfirst($date->translatedFormat('l d \d\e F \d\e\l Y'));
                $hora = $date->translatedFormat('h:i A');

                $nombre = strtok($client->name, " ");
                $mensaje = "¡Hola *{$nombre}*! 🗓️\n\nConfirmamos tu cita en *Fase 2* para:\n*Servicio:* {$schedule->title}\n*Fecha:* {$fecha}\n*Hora:* {$hora}\n\nSi tienes alguna duda o necesitas reagendar, por favor contáctanos por esta misma vía. ¡Te esperamos! ✨";
                $this->whatsapp->sendMessage($client->phone, $mensaje);
            }
        }

        if ($request->get('is_express')) {
            $turnNumber = \App\Http\Controllers\QueueController::generateNextTurnNumber();
            \App\Models\TurnQueue::create([
                'schedule_id' => $schedule->id,
                'turn_number' => $turnNumber,
                'status' => 'waiting'
            ]);
        }

		return ['success' => true]; 
	}

    public function update($id,ScheduleRequest $request){
        $start = $request->get('start');
        $end = $request->get('end');

        $overlapping = Schedule::where('id', '!=', $id)->where(function($query) use ($start, $end) {
            $query->where('start', '<', $end)
                  ->where('end', '>', $start);
        })->exists();

        if ($overlapping) {
            return response(['success' => false, 'error' => 'El horario se empalma con otra cita existente.'], 400);
        }

    	$schedule = Schedule::find($id);
		$schedule->title = $request->get('title');
		$schedule->description = $request->get('description');
		$schedule->start = $start;
		$schedule->end = $end;
		$schedule->color = $request->get('color');
		$schedule->allDay = $request->get('allDay');
		$schedule->client_id = $request->get('client_id');
        $schedule->package_id = $request->get('package_id');
        if ($request->has('service_id')) {
            $schedule->service_id = $request->get('service_id');
        }
		$schedule->save();

        if ($schedule->client_id) {
            $client = Client::find($schedule->client_id);
            if ($client && $client->phone) {
                $date = Carbon::parse($schedule->start)->locale('es');
                $fechaHora = ucfirst($date->translatedFormat('l d \d\e F \d\e\l Y \a \l\a\s h:i A'));

                $nombre = strtok($client->name, " ");
                $mensaje = "¡Hola *{$nombre}*! 🔄\n\nTu cita en *Fase 2* ha sido actualizada.\nTu nuevo horario es: *{$fechaHora}*.";
                $this->whatsapp->sendMessage($client->phone, $mensaje);
            }
        }

    	return ['success' => true]; 
    }

    public function delete($id){
    	$schedule = Schedule::with('tracking')->find($id);
        if ($schedule && $schedule->tracking) {
            $schedule->tracking->delete();
        }
		if($schedule) {
            $schedule->delete();
        }
    	return ['success' => true]; 
    }

    public function getAll(){
		return response(Schedule::with('client', 'package.type', 'tracking', 'service')->get(), 200)
		->header('Content-Type', 'application/json');
    }

    public function find($id){
        $schedule = Schedule::with('client', 'package.type', 'tracking', 'service')->find($id);
        return response($schedule, 200)->header('Content-Type', 'application/json');
    }

    public function checkIn($id, Request $request){
        $schedule = Schedule::with(['package.type', 'tracking'])->find($id);
        if (!$schedule || !$schedule->package_id) {
            return response(['error' => 'No package linked to this schedule'], 400);
        }

        if ($schedule->tracking) {
            return response(['error' => 'Already checked in'], 400);
        }

        $track = new PackageTracking;
        $track->user_id = $request->get('user_id'); // El frontend debe enviarlo
        $track->package_id = $schedule->package_id;
        $track->schedule_id = $schedule->id;
        $track->is_taken = 1;
        $track->description = 'Asistencia confirmada desde agenda: ' . $schedule->title;
        $track->scheduled_date = $schedule->start;
        $track->save();

        $package = $schedule->package;
        $tracksCount = PackageTracking::where('package_id', $package->id)->count();

        if($package->type->session_count == $tracksCount && $package->is_paid == 0){
            $package->is_completed = true;
            $package->save();
        }

        $catPackage = CatPackage::with(['complements'])->find($package->type->id);
                
        foreach ($catPackage->complements as $_complement){
            if($_complement["pill_id"] != null){ 
                $pillInventory = PillInventory::where('pill_id',$_complement["pill_id"])->first();
                if($pillInventory) {
                    $pillInventory->count = ((int)$pillInventory->count - (int)$_complement["count"]);
                    $pillInventory->save();
                }
            }
            if($_complement["product_id"] != null){
                $productInventory = ProductInventory::where('product_id',$_complement["product_id"])->first();
                if($productInventory) {
                    $productInventory->count = ((int)$productInventory->count - (int)$_complement["count"]);
                    $productInventory->save();
                }
            }
        }

        // AGREGAR A LA COLA DE TURNOS
        $turnNumber = \App\Http\Controllers\QueueController::generateNextTurnNumber();
        \App\Models\TurnQueue::create([
            'schedule_id' => $schedule->id,
            'turn_number' => $turnNumber,
            'status' => 'waiting'
        ]);

        // Si no hay nadie en progreso, avanzar automáticamente a este como 'in_progress'
        $inProgress = \App\Models\TurnQueue::where('status', 'in_progress')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->first();
            
        if (!$inProgress) {
            $firstWaiting = \App\Models\TurnQueue::where('status', 'waiting')
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->orderBy('id', 'asc')
                ->first();
            if ($firstWaiting) {
                $firstWaiting->status = 'in_progress';
                $firstWaiting->save();
            }
        }

        return response(['success' => true], 200)->header('Content-Type', 'application/json');
    }
}