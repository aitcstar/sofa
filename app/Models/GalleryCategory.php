<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(GalleryItem::class, 'category_id');
    }

    public function activeItems()
    {
        return $this->hasMany(GalleryItem::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
    }

    public function featuredItems()
    {
        return $this->hasMany(GalleryItem::class, 'category_id')
                    ->where('is_active', true)
                    ->where('is_featured', true)
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Accessors
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->where('is_active', true)->count();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Methods
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function generateUniqueSlug(): string
    {
        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get category statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_items' => $this->items()->count(),
            'active_items' => $this->items()->where('is_active', true)->count(),
            'featured_items' => $this->items()->where('is_featured', true)->count(),
            'total_views' => $this->items()->sum('views_count'),
            'total_likes' => $this->items()->sum('likes_count'),
        ];
    }

    /**
     * Get default categories.
     */
    public static function getDefaultCategories(): array
    {
        return [
            [
                'name' => 'عمارات سكنية',
                'slug' => 'residential-buildings',
                'description' => 'مشاريع العمارات السكنية والمجمعات السكنية',
                'sort_order' => 1,
                'metadata' => ['project_types' => ['building', 'compound']]
            ],
            [
                'name' => 'الفلل',
                'slug' => 'villas',
                'description' => 'تصاميم الفلل الفاخرة والقصور',
                'sort_order' => 2,
                'metadata' => ['project_types' => ['villa']]
            ],
            [
                'name' => 'الشقق الفندقية',
                'slug' => 'hotel-apartments',
                'description' => 'مشاريع الشقق الفندقية والمنتجعات',
                'sort_order' => 3,
                'metadata' => ['project_types' => ['hotel_apartments']]
            ],
            [
                'name' => 'المشاريع التجارية',
                'slug' => 'commercial-projects',
                'description' => 'المباني التجارية والمكاتب والمولات',
                'sort_order' => 4,
                'metadata' => ['project_types' => ['commercial']]
            ]
        ];
    }

    /**
     * Create default categories.
     */
    public static function createDefaultCategories(): void
    {
        foreach (static::getDefaultCategories() as $categoryData) {
            static::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
}
