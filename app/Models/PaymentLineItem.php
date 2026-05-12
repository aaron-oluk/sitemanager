<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLineItem extends Model
{
    protected $fillable = [
        'payment_id',
        'item_type',
        'label',
        'unit_cost',
        'tax_amount',
        'transaction_fee',
        'total_amount',
        'currency',
        'reference_id',
    ];

    protected $casts = [
        'unit_cost'       => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'transaction_fee' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
