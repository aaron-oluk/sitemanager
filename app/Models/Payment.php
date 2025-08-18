<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_date',
        'status',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
