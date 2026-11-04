<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'category_id',
        'address',
        'number',
        'email',
        'category_rates',
        'amount_type',
        'old_balance',
        'area',
        'report_send_status',
    ];

    protected $casts = [
    'category_rates' => 'array',
];

public function scopeActive($query)
{
    return $query->where('is_paused', '0'); // Or use: ->where('active', 0)
}

   public function invoices()
{
    return $this->hasMany(Invoice::class);
}

 public function deliveries()
{
   return $this->hasMany(Delivery::class, 'customer_id', 'id');
}
      public function categoryRates(){
        return $this->hasMany(CustomerCategoryRate::class,'customer_id','id');
    }

  public function payments()
{
    return $this->hasMany(Payment::class, 'customer_id', 'id');
}


public function users()
{
    return $this->hasMany(User::class,);
}

}
