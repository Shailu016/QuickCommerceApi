<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'category_id', 'description', 'image'];

   

    //cart product relationship
    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class);
    }

    //category relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImagePathAttribute($value)
    {
        return asset('images/' . $value);
    }
}
