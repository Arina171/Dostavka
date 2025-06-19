<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;     
use App\Models\Manufacturer; 
use App\Models\Order;       
use App\Models\ComparisonListItem; 

class Product extends Model
{
    use HasFactory; 

    protected $fillable = [
        'name',            
        'slug',            
        'description',    
        'price',           
        'stock_quantity',  
        'category_id',     
        'manufacturer_id', 
        'image_url',       
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function orders()
    {
        // Указываем связь многие ко многим с моделью Order через промежуточную таблицу `order_products`.
        return $this->belongsToMany(Order::class, 'order_products')
                    ->withPivot('quantity', 'price_at_order')
                    ->withTimestamps();
    }

    public function comparisonListItems()
    {
        return $this->hasMany(ComparisonListItem::class);
    }
}