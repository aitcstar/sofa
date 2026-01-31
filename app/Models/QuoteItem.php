<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'package_id',
        'item_name',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'sort_order'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    // العلاقات
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Methods
    public function calculateTotal()
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();

        // إعادة حساب إجماليات عرض السعر
        $this->quote->calculateTotals();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // حساب السعر الإجمالي تلقائياً
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::updating(function ($item) {
            // إعادة حساب السعر الإجمالي عند التحديث
            if ($item->isDirty(['quantity', 'unit_price'])) {
                $item->total_price = $item->quantity * $item->unit_price;
            }
        });

        static::saved(function ($item) {
            // إعادة حساب إجماليات عرض السعر
            $item->quote->calculateTotals();
        });

        static::deleted(function ($item) {
            // إعادة حساب إجماليات عرض السعر
            $item->quote->calculateTotals();
        });
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_name', 'item_name_ar');
    }
}
