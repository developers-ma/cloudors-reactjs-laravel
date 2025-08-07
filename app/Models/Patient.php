<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dossier_number',
        'dob',
        'phone',
        'email',
        'sexe',
        'cin',
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}