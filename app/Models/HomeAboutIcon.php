<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeAboutIcon extends Model
{
    protected $fillable = ['home_about_section_id', 'icon', 'title_en', 'title_ar', 'order'];

    public function section()
    {
        return $this->belongsTo(HomeAboutSection::class, 'home_about_section_id');
    }
}
