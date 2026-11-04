<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;


    protected $fillable = [
        'date',
        'opening_stock',
        'product_type',
        'used',
        'received',
        'closing_stock',
        'wastage'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class,'product_type','id');
    }
}
