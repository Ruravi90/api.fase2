<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use \App\Traits\BelongsToTenant;

    use HasFactory;

    protected $fillable = [
        'client_id',
        'blood_type',
        'allergies',
        'pathological_history',
        'non_pathological_history',
        'family_history',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function clinicalNotes()
    {
        return $this->hasMany(ClinicalNote::class);
    }
}
