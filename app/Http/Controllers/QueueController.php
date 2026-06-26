<?php

namespace App\Http\Controllers;

use App\Models\TurnQueue;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QueueController extends Controller
{
    // Obtener la cola activa
    public function getActiveQueue()
    {
        $queue = TurnQueue::with(['schedule.client', 'schedule.package.type'])
            ->whereIn('status', ['waiting', 'in_progress'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($queue);
    }

    // Avanzar turno (finaliza actual y pasa al siguiente)
    public function advanceTurn(Request $request)
    {
        // El actual pasa a completed
        $current = TurnQueue::where('status', 'in_progress')
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($current) {
            $current->status = 'completed';
            $current->save();
        }

        // El siguiente 'waiting' pasa a 'in_progress'
        $next = TurnQueue::where('status', 'waiting')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'asc')
            ->first();

        if ($next) {
            $next->status = 'in_progress';
            $next->save();
        }

        return response()->json(['success' => true, 'current' => $next]);
    }

    // Generar siguiente número de turno alfanumérico secuencial del día
    // (Este método se usará desde ScheduleController al hacer checkIn)
    public static function generateNextTurnNumber()
    {
        $lastTurn = TurnQueue::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTurn) {
            return 'A-1';
        }

        $parts = explode('-', $lastTurn->turn_number);
        if (count($parts) != 2) {
            return 'A-1';
        }

        $letter = $parts[0];
        $number = (int)$parts[1];

        $number++;
        if ($number > 99) {
            $number = 1;
            $letter = ++$letter;
        }

        return $letter . '-' . $number;
    }
}
