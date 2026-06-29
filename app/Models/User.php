<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use \App\Traits\BelongsToTenant;

    use HasApiTokens, HasRoles, Notifiable;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'lastname',
        'motherlastname',
        'phone_mobile',
        'username',
        'avatar',
        'initials',
        'professional_license',
        'specialty',
        'university',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function chatConversations()
    {
        return $this->belongsToMany(ChatConversation::class, 'chat_conversation_user')->withPivot('last_read_at');
    }
}

