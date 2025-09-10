<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessSection extends Model
{
    protected $fillable = [
        'title_ar', 'title_en', 'desc_ar', 'desc_en','button_text_en','button_text_ar',
        'avatar', 'name', 'units', 'status', 'progress'
    ];

    public function steps()
    {
        return $this->hasMany(ProcessStep::class)->orderBy('order');
    }
}
