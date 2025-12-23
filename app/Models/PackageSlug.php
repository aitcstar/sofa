<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageSlug extends Model
{
    protected $fillable = ['package_id', 'slug'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
