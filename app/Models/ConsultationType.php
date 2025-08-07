<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationType extends Model
{
    use HasFactory;

    public $timestamps = false; // Pas de created_at/updated_at pour cette table

    protected $fillable = [
        'name',
        'price',
    ];
}
