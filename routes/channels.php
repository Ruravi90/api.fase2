<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    \Log::info("Autorizando canal chat.{$conversationId} para usuario {$user->id}");
    
    if (str_starts_with($conversationId, 'user_')) {
        \Log::info("Resultado de autorización chat (nuevo): true");
        return true;
    }

    $exists = $user->chatConversations()->where('chat_conversations.id', $conversationId)->exists();
    \Log::info("Resultado de autorización chat: " . ($exists ? 'true' : 'false'));
    return $exists;
});

Broadcast::channel('online', function ($user) {
    \Log::info("Autorizando canal online para usuario {$user->id}");
    return ['id' => $user->id, 'name' => $user->name];
});
