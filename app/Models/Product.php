<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'specifications_ar',
        'specifications_en',
        'price',
        'discount_price',
        'sku',
        'slug',
        'images',
        'dimensions',
        'material_ar',
        'material_en',
        'color_ar',
        'color_en',
        'stock_quantity',
        'is_active',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'images' => 'array',
        'dimensions' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getSpecificationsAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->specifications_ar : $this->specifications_en;
    }

    public function getMaterialAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->material_ar : $this->material_en;
    }

    public function getColorAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->color_ar : $this->color_en;
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getMainImageAttribute()
    {
        return $this->images[0] ?? null;
    }

    // Mutators
    public function setNameArAttribute($value)
    {
        $this->attributes['name_ar'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name_ar');
    }


    public function orders()
{
    return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
                ->withPivot('quantity', 'price');
}
}

