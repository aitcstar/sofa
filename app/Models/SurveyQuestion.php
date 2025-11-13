<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'type',
        'is_required',
        'order',
        'desc_en',
        'desc_ar',
    ];

    public function options()
    {
        return $this->hasMany(SurveyOption::class)->orderBy('order');
    }
}
