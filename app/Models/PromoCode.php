<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'percentage',
        'is_active',
        'max_uses',
        'uses_count',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'percentage' => 'decimal:2',
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }
}
