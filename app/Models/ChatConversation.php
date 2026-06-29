<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = ['type', 'name', 'last_message', 'last_message_at'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_conversation_user')->withPivot('last_read_at');
    }
}
