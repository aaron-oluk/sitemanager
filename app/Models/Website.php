<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'host_server',
        'deployment_date',
        'amount_paid',
        'status',
        'description',
        'client_name',
        'client_email',
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
