<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'package_id',
        'design_id',
        'item_name_ar',
        'item_name_en',
        'quantity',
        'dimensions',
        'material_ar',
        'material_en',
        'color_ar',
        'color_en',
        'background_color',
        'image_path',
        'description',
        'default_price',
        'default_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // العلاقات
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function images()
{
    return $this->hasMany(PackageImage::class, 'package_id', 'package_id');
}
/*public function packageUnitItems()
{
    return $this->hasMany(PackageUnitItem::class);
}*/

public function packageUnitItems()
{
    return $this->hasMany(PackageUnitItem::class, 'item_id');
}



}
