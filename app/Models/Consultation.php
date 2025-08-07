<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'date',
        'reason',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'prescriptions',
        'documents',
        'is_completed',
    ];

    protected $casts = [
        'date' => 'date',
        'prescriptions' => 'array',
        'documents' => 'array',
        'is_completed' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
