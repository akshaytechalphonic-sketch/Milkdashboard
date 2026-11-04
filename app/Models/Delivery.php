<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable=[
    'customer_id',
    'date',
    'month',
    'year',
    'litres',
    'rate',
    'product_id',
    'total',
    'customer_rates'
    ];


    protected $casts = [
    'customer_rates' => 'array',
];
   public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}

 public function category()
{
    return $this->belongsTo(Category::class, 'product_id', 'id');
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
