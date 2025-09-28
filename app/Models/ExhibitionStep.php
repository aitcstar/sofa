<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitionStep extends Model
{
    protected $fillable = ['exhibition_id', 'title_ar', 'title_en', 'icon', 'sort_order'];

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }
}

