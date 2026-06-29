<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSession extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'session_id',
        'name',
        'status',
    ];

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'role_whatsapp_session', 'whatsapp_session_id', 'role_id');
    }
}
