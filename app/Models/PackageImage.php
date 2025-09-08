<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
