<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'turn_number',
        'status',
        'notified_at'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
