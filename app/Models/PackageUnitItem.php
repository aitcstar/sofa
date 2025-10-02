<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageUnitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'unit_id',
        'item_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
