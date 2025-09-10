<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyToFurnishSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en','title_ar','desc_en','desc_ar',
        'whatsapp','start_order_link','image'
    ];
}
