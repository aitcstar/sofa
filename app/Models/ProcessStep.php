<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    protected $fillable = [
        'process_section_id', 'icon', 'title_ar', 'title_en',
        'desc_ar', 'desc_en', 'order'
    ];

    public function section()
    {
        return $this->belongsTo(ProcessSection::class);
    }
}
