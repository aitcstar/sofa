<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'message',
        'rating',
        'image',
        'package_id',
        'status',
        'user_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
