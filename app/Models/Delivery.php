<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory; 

    protected $fillable = [
        'order_id',         
        'delivery_method', 
        'delivery_address', 
        'delivery_date',    
        'delivery_status',  
        'courier_id',       
    ];

    protected $casts = [
        'delivery_date' => 'datetime', 
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}