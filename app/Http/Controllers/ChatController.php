<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json([]);

        // 1. Obtener conversaciones existentes
        $conversations = $user->chatConversations()->with(['users'])->orderBy('last_message_at', 'desc')->get()->map(function($conv) use ($user) {
            if ($conv->type === 'direct') {
                $otherUser = $conv->users->where('id', '!=', $user->id)->first();
                $conv->name = $otherUser ? $otherUser->name . ' ' . $otherUser->lastname : 'Usuario';
                $conv->other_user_id = $otherUser ? $otherUser->id : null;
            }
            return $conv;
        });

        // 2. Obtener usuarios con los que aún no hay conversación
        $existingDirectUserIds = $conversations->where('type', 'direct')->map(function($conv) use ($user) {
            $otherUser = $conv->users->where('id', '!=', $user->id)->first();
            return $otherUser ? $otherUser->id : null;
        })->filter()->toArray();

        $otherUsers = \App\Models\User::where('id', '!=', $user->id)
            ->whereNotIn('id', $existingDirectUserIds)
            ->get()->map(function($u) {
                return [
                    'id' => 'user_' . $u->id,
                    'name' => trim($u->name . ' ' . $u->lastname),
                    'type' => 'direct',
                    'other_user_id' => $u->id,
                    'last_message' => null,
                    'last_message_at' => null,
                ];
            });

        return response()->json($conversations->concat($otherUsers));
    }

    public function show(Request $request, $id)
    {
        if (str_starts_with($id, 'user_')) {
            return response()->json([]); // No hay mensajes aún
        }

        $conversation = \App\Models\ChatConversation::findOrFail($id);
        $conversation->users()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }

    public function store(Request $request, $id)
    {
        $request->validate(['body' => 'required|string']);
        $user = $request->user();

        if (str_starts_with($id, 'user_')) {
            $targetUserId = str_replace('user_', '', $id);
            $conversation = \App\Models\ChatConversation::create([
                'type' => 'direct',
                'last_message' => $request->body,
                'last_message_at' => now(),
            ]);
            $conversation->users()->attach([$user->id, $targetUserId]);
        } else {
            $conversation = \App\Models\ChatConversation::findOrFail($id);
            $conversation->update([
                'last_message' => $request->body,
                'last_message_at' => now()
            ]);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $request->body,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        $msg = $message->load('sender')->toArray();
        $msg['new_conversation_id'] = $conversation->id; // Para actualizar el frontend

        return response()->json($msg);
    }
}
