<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TurnQueue;
use App\Services\OpenWAService;
use Carbon\Carbon;

class NotifyUpcomingTurns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:notify-upcoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify clients when their turn is approaching';

    /**
     * Execute the console command.
     */
    public function handle(OpenWAService $whatsapp)
    {
        // Obtener a los siguientes 2 en la cola de 'waiting' que no han sido notificados
        $upcoming = TurnQueue::with(['schedule.client'])
            ->where('status', 'waiting')
            ->whereDate('created_at', Carbon::today())
            ->whereNull('notified_at')
            ->orderBy('id', 'asc')
            ->take(2)
            ->get();

        foreach ($upcoming as $index => $turn) {
            $client = $turn->schedule->client;
            if ($client && $client->phone) {
                $nombre = strtok($client->name, " ");
                $personas = $index + 1; // 1 o 2 personas adelante
                $mensaje = "¡Hola *{$nombre}*! 🔔\n\nTu turno *{$turn->turn_number}* está por llegar. Faltan aproximadamente {$personas} persona(s) para que pases.\nPor favor acércate a recepción.";
                
                $whatsapp->sendMessage($client->phone, $mensaje);

                $turn->notified_at = Carbon::now();
                $turn->save();
            }
        }

        $this->info('Notificaciones enviadas exitosamente.');
    }
}
