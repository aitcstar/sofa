<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UnitDesign extends Pivot
{
    use HasFactory;

    protected $table = 'unit_designs';

    protected $fillable = [
        'unit_id',
        'design_id',
        'is_default',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
