<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([]);
        }

        $conversations = $user->chatConversations()->with(['users'])->orderBy('last_message_at', 'desc')->get();
        return response()->json($conversations);
    }

    public function show(Request $request, $id)
    {
        $conversation = \App\Models\ChatConversation::findOrFail($id);
        
        // Mark as read
        $conversation->users()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);

        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        $conversation = \App\Models\ChatConversation::findOrFail($id);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        $conversation->update([
            'last_message' => $request->body,
            'last_message_at' => now()
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json($message->load('sender'));
    }
}
