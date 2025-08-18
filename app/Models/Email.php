<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_address',
        'provider',
        'hosting_plan',
        'monthly_cost',
        'start_date',
        'renewal_date',
        'status',
        'notes',
        'associated_website',
    ];

    protected $casts = [
        'start_date' => 'date',
        'renewal_date' => 'date',
        'monthly_cost' => 'decimal:2',
    ];
}
