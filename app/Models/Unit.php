<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'name_ar',
        'name_en',
        'type',
        'description_en',
        'description_ar',
    ];

    // العلاقات
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /*public function designs()
    {
        return $this->belongsToMany(Design::class, 'unit_designs');
    }*/
    public function designs()
{
    return $this->belongsToMany(Design::class, 'unit_designs')
                ->withPivot('is_default')
                ->withTimestamps();
}



    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function itemsLimited()
{
    return $this->hasMany(Item::class)->take(6);
}

public function images()
{
    return $this->hasMany(UnitImage::class);
}

/*public function packageUnitItems()
{
    return $this->hasMany(PackageUnitItem::class);
}*/
public function unitImages()
{
    return $this->hasMany(UnitImage::class);
}
public function packageUnitItems()
{
    return $this->hasMany(PackageUnitItem::class, 'unit_id');
}


}
