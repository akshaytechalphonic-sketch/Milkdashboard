<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategoryRate extends Model
{
    use HasFactory;

    protected $table = 'customers_category_rates';
    protected $fillable = [
        'customer_id',
        'category_id',
        'rate'
    ];
}
