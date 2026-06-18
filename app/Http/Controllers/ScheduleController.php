<?php
namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Schedule;
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
		$schedule = new Schedule;
		$schedule->title = $request->get('title');
		$schedule->description = $request->get('description');
		$schedule->start = $request->get('start');
		$schedule->end = $request->get('end');
		$schedule->color = $request->get('color');
		$schedule->allDay = $request->get('allDay');
		$schedule->client_id = $request->get('client_id');
		$schedule->save();

        if ($schedule->client_id) {
            $client = Client::find($schedule->client_id);
            // El modelo de cliente usualmente tiene la propiedad 'phone'. Ajusta si se llama 'cellphone' u otro.
            if ($client && $client->phone) {
                $date = Carbon::parse($schedule->start)->locale('es');
                $fecha = ucfirst($date->translatedFormat('l d \d\e F \d\e\l Y'));
                $hora = $date->translatedFormat('h:i A');

                $nombre = strtok($client->name, " "); // Primer nombre
                $mensaje = "¡Hola *{$nombre}*! 🗓️\n\nConfirmamos tu cita en *Fase 2* para:\n*Servicio:* {$schedule->title}\n*Fecha:* {$fecha}\n*Hora:* {$hora}\n\nSi tienes alguna duda o necesitas reagendar, por favor contáctanos por esta misma vía. ¡Te esperamos! ✨";
                $this->whatsapp->sendMessage($client->phone, $mensaje);
            }
        }

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