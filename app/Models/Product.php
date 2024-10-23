<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Product extends Model
{


    protected $fillable = [
        'category_id',
        'photo',
        'name',
        'price',
        'description',
        'stock',
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Use model events to delete related order items
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            // Delete all related order items before deleting the product
            $product->orderItems()->delete();
        });
    }

}
