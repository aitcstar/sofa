<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyChooseSection extends Model
{
    protected $fillable = ['title_en', 'title_ar', 'desc_en', 'desc_ar'];

    public function items()
    {
        return $this->hasMany(WhyChooseItem::class);
    }
}
