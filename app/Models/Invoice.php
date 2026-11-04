<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'month',
        'year',
        'total_litres',
        'rate',
        'total_amount',
        'old_balance',
        'advance_amount',
        'advance_used',
        'received_amount',
        'due_amount',
        'payment_mode',
        'status'
    ];

    protected $casts = [
        'customer_rates' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'id');
    }

    public function deliveries()
    {
        return $this->hasManyThrough(
            Delivery::class,
            Customer::class,
            'id',             // Foreign key on Customer table
            'customer_id',    // Foreign key on Delivery table
            'customer_id',    // Local key on Invoice table
            'id'              // Local key on Customer table
        );
    }
}
