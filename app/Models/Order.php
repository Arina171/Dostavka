<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',     
        'order_date',  
        'status',      
        'total_price', 
    ];

    protected $casts = [
        'order_date' => 'datetime', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        // Указываем связь многие ко многим с моделью Product через промежуточную таблицу `order_products`.
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('quantity', 'price_at_order')
                    ->withTimestamps();
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}