<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WhatsappSession;
use Illuminate\Support\Facades\Log;

class OpenwaController extends Controller
{
    private function getApiUrl()
    {
        return env('OPENWA_API_URL', 'http://localhost:2785/api');
    }

    private function getApiKey()
    {
        return env('OPENWA_API_KEY', 'dev-admin-key');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = WhatsappSession::with('roles');
        
        // Si el usuario no tiene rol super_admin, filtramos por los roles que tiene el usuario
        if (!$user->hasRole('super_admin')) {
            $userRoles = $user->roles->pluck('id')->toArray();
            $query->whereHas('roles', function ($q) use ($userRoles) {
                $q->whereIn('role_id', $userRoles);
            });
        }
        
        $sessions = $query->get();
        
        // Consultar el estado de cada sesión en OpenWA
        foreach ($sessions as $session) {
            try {
                $response = Http::withHeaders([
                    'X-API-Key' => $this->getApiKey()
                ])->get($this->getApiUrl() . '/sessions/' . $session->session_id . '/status');
                
                if ($response->successful()) {
                    $session->status = $response->json('status');
                    $session->save();
                }
            } catch (\Exception $e) {
                Log::error("Error checking OpenWA session status: " . $e->getMessage());
                // Mantenemos el estado actual en BD o ponemos UNKNOWN
            }
        }
        
        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string|unique:whatsapp_sessions,session_id',
            'name' => 'required|string',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        // Crear la sesión en OpenWA
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->getApiKey()
            ])->post($this->getApiUrl() . '/sessions', [
                'sessionId' => $request->session_id,
                'name' => $request->name
            ]);

            if (!$response->successful()) {
                return response()->json(['message' => 'Error creating session in OpenWA', 'error' => $response->json()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot connect to OpenWA API'], 500);
        }

        $session = WhatsappSession::create([
            'session_id' => $request->session_id,
            'name' => $request->name,
            'status' => 'STOPPED'
        ]);

        $session->roles()->sync($request->role_ids);

        return response()->json($session, 201);
    }

    public function start($id)
    {
        $session = WhatsappSession::findOrFail($id);
        
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->getApiKey()
            ])->post($this->getApiUrl() . '/sessions/' . $session->session_id . '/start');

            if ($response->successful()) {
                return response()->json(['message' => 'Session started']);
            }
            return response()->json(['message' => 'Error starting session', 'error' => $response->json()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot connect to OpenWA API'], 500);
        }
    }

    public function getQr($id)
    {
        $session = WhatsappSession::findOrFail($id);
        
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->getApiKey()
            ])->get($this->getApiUrl() . '/sessions/' . $session->session_id . '/qr');

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['message' => 'Error getting QR code', 'error' => $response->json()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot connect to OpenWA API'], 500);
        }
    }

    public function destroy($id)
    {
        $session = WhatsappSession::findOrFail($id);
        
        try {
            Http::withHeaders([
                'X-API-Key' => $this->getApiKey()
            ])->delete($this->getApiUrl() . '/sessions/' . $session->session_id);
        } catch (\Exception $e) {
            // Ignorar el error si OpenWA no responde, igual la borramos de nuestra BD
            Log::error("Error deleting OpenWA session: " . $e->getMessage());
        }

        $session->delete();
        return response()->json(['message' => 'Session deleted']);
    }
}
