<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'registrar',
        'registration_date',
        'expiry_date',
        'annual_cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'annual_cost' => 'decimal:2',
    ];
}
