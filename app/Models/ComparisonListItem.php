<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonListItem extends Model
{
    use HasFactory; 

    protected $fillable = [
        'comparison_list_id', 
        'product_id',         
    ];

    public function comparisonList()
    {

        return $this->belongsTo(ComparisonList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}