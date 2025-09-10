<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyChooseItem extends Model
{
    protected $fillable = [
        'why_choose_section_id',
        'title_en','title_ar',
        'desc_en','desc_ar',
        'icon'
    ];

    public function section()
    {
        return $this->belongsTo(WhyChooseSection::class);
    }
}
