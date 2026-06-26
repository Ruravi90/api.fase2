<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ClinicalNote extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'medical_record_id',
        'schedule_id',
        'doctor_id',
        'subjective',
        'objective',
        'analysis',
        'plan',
        'diagnoses',
        'weight',
        'blood_pressure',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'status',
        'signed_at',
    ];

    // Cifrar los campos sensibles (LFPDPPP)
    protected $casts = [
        'subjective' => 'encrypted',
        'objective' => 'encrypted',
        'analysis' => 'encrypted',
        'plan' => 'encrypted',
        'diagnoses' => 'array',
        'signed_at' => 'datetime',
    ];

    // Configurar logs de auditoría (NOM-024)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
