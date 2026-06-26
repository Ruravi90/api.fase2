<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicalNote;
use App\Models\MedicalRecord;
use App\Models\Client;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClinicalNoteController extends Controller
{
    /**
     * Get the medical record and clinical history for a client.
     * @param int $clientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory($clientId)
    {
        $medicalRecord = MedicalRecord::firstOrCreate(
            ['client_id' => $clientId],
            ['blood_type' => null] // Default empty record
        );
        $medicalRecord->load('client');

        $notes = ClinicalNote::with('doctor:id,name,lastname')
            ->where('medical_record_id', $medicalRecord->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'medical_record' => $medicalRecord,
            'history' => $notes
        ]);
    }

    /**
     * Save a draft of a clinical note.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|integer',
            'client_id' => 'required|integer',
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'analysis' => 'nullable|string',
            'plan' => 'nullable|string',
            'diagnoses' => 'nullable|array',
            'weight' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'oxygen_saturation' => 'nullable|integer',
        ]);

        $medicalRecord = MedicalRecord::firstOrCreate(
            ['client_id' => $validated['client_id']]
        );

        $note = ClinicalNote::updateOrCreate(
            [
                'schedule_id' => $validated['schedule_id'],
                'medical_record_id' => $medicalRecord->id,
            ],
            array_merge($validated, [
                'doctor_id' => Auth::id() ?? 1, // Fallback if no auth
                'status' => 'draft',
            ])
        );

        return response()->json([
            'message' => 'Borrador guardado',
            'note' => $note
        ]);
    }

    /**
     * Sign and close a clinical note.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function signNote(Request $request, $id)
    {
        $note = ClinicalNote::findOrFail($id);

        if ($note->status === 'signed') {
            return response()->json(['message' => 'Esta nota ya fue firmada y es inalterable.'], 403);
        }

        // Validate final data
        $validated = $request->validate([
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'analysis' => 'nullable|string',
            'plan' => 'nullable|string',
            'diagnoses' => 'nullable|array',
            'weight' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
        ]);

        // Guardar la información final y firmar
        $note->fill($validated);
        $note->status = 'signed';
        $note->signed_at = Carbon::now();
        $note->doctor_id = Auth::id() ?? 1;
        $note->save();

        // Actualizar el estado del Schedule a completado si es necesario
        $schedule = Schedule::find($note->schedule_id);
        if ($schedule) {
            $schedule->status = 'COMPLETED'; // Assuming this status exists
            $schedule->save();
        }

        return response()->json([
            'message' => 'Nota firmada digitalmente con éxito',
            'note' => $note
        ]);
    }
}
