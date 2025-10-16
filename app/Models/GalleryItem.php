<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'image_path',
        'thumbnail_path',
        'type',
        'images',
        'project_type',
        'units_count',
        'location',
        'area',
        'completion_year',
        'features',
        'colors',
        'sort_order',
        'is_featured',
        'is_active',
        'views_count',
        'likes_count',
        'metadata'
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'colors' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'area' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(GalleryCategory::class, 'category_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProjectType($query, $projectType)
    {
        return $query->where('project_type', $projectType);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc')->orderBy('likes_count', 'desc');
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? 
            asset('storage/' . $this->thumbnail_path) : 
            $this->image_url;
    }

    public function getProjectTypeTextAttribute(): string
    {
        return match($this->project_type) {
            'building' => 'عمارة سكنية',
            'compound' => 'مجمع سكني',
            'hotel_apartments' => 'شقق فندقية',
            'villa' => 'فيلا',
            'commercial' => 'مشروع تجاري',
            default => 'غير محدد'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'image' => 'صورة',
            'video' => 'فيديو',
            '360_view' => 'عرض 360 درجة',
            default => 'غير محدد'
        };
    }

    public function getFormattedAreaAttribute(): ?string
    {
        return $this->area ? number_format($this->area, 0) . ' م²' : null;
    }

    public function getAllImagesAttribute(): array
    {
        $allImages = [];
        
        // Add main image
        if ($this->image_path) {
            $allImages[] = [
                'url' => $this->image_url,
                'thumbnail' => $this->thumbnail_url,
                'is_main' => true
            ];
        }

        // Add additional images
        if ($this->images && is_array($this->images)) {
            foreach ($this->images as $image) {
                $allImages[] = [
                    'url' => asset('storage/' . $image),
                    'thumbnail' => asset('storage/' . $image),
                    'is_main' => false
                ];
            }
        }

        return $allImages;
    }

    /**
     * Methods
     */
    public function incrementViews(): bool
    {
        return $this->increment('views_count');
    }

    public function incrementLikes(): bool
    {
        return $this->increment('likes_count');
    }

    public function decrementLikes(): bool
    {
        return $this->decrement('likes_count');
    }

    public function addImage(string $imagePath): bool
    {
        $images = $this->images ?? [];
        $images[] = $imagePath;
        
        return $this->update(['images' => $images]);
    }

    public function removeImage(string $imagePath): bool
    {
        $images = $this->images ?? [];
        $images = array_filter($images, fn($img) => $img !== $imagePath);
        
        // Delete the file
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        
        return $this->update(['images' => array_values($images)]);
    }

    public function addFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            return $this->update(['features' => $features]);
        }
        return false;
    }

    public function removeFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        $features = array_filter($features, fn($f) => $f !== $feature);
        
        return $this->update(['features' => array_values($features)]);
    }

    public function addColor(string $color): bool
    {
        $colors = $this->colors ?? [];
        if (!in_array($color, $colors)) {
            $colors[] = $color;
            return $this->update(['colors' => $colors]);
        }
        return false;
    }

    public function removeColor(string $color): bool
    {
        $colors = $this->colors ?? [];
        $colors = array_filter($colors, fn($c) => $c !== $color);
        
        return $this->update(['colors' => array_values($colors)]);
    }

    /**
     * Delete associated files when deleting the item.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            // Delete main image
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }

            // Delete thumbnail
            if ($item->thumbnail_path && Storage::disk('public')->exists($item->thumbnail_path)) {
                Storage::disk('public')->delete($item->thumbnail_path);
            }

            // Delete additional images
            if ($item->images && is_array($item->images)) {
                foreach ($item->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
        });
    }

    /**
     * Get similar items.
     */
    public function getSimilarItems(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('id', '!=', $this->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('category_id', $this->category_id)
                      ->orWhere('project_type', $this->project_type);
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search items.
     */
    public static function search(string $query): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhereJsonContains('features', $query)
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            });
    }

    /**
     * Get items by filters.
     */
    public static function getFiltered(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = static::where('is_active', true);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['min_area'])) {
            $query->where('area', '>=', $filters['min_area']);
        }

        if (!empty($filters['max_area'])) {
            $query->where('area', '<=', $filters['max_area']);
        }

        if (!empty($filters['completion_year'])) {
            $query->where('completion_year', $filters['completion_year']);
        }

        if (!empty($filters['features'])) {
            foreach ($filters['features'] as $feature) {
                $query->whereJsonContains('features', $feature);
            }
        }

        if (!empty($filters['colors'])) {
            foreach ($filters['colors'] as $color) {
                $query->whereJsonContains('colors', $color);
            }
        }

        if (!empty($filters['is_featured'])) {
            $query->where('is_featured', true);
        }

        return $query;
    }

    /**
     * Get popular items.
     */
    public static function getPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('views_count', 'desc')
            ->orderBy('likes_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured items.
     */
    public static function getFeatured(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent items.
     */
    public static function getRecent(int $limit = 12): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get available project types.
     */
    public static function getAvailableProjectTypes(): array
    {
        return static::where('is_active', true)
            ->whereNotNull('project_type')
            ->distinct()
            ->pluck('project_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => match($type) {
                        'building' => 'عمارة سكنية',
                        'compound' => 'مجمع سكني',
                        'hotel_apartments' => 'شقق فندقية',
                        'villa' => 'فيلا',
                        'commercial' => 'مشروع تجاري',
                        default => $type
                    }
                ];
            })
            ->toArray();
    }

    /**
     * Get available features.
     */
    public static function getAvailableFeatures(): array
    {
        $features = [];
        
        static::where('is_active', true)
            ->whereNotNull('features')
            ->get()
            ->each(function ($item) use (&$features) {
                if ($item->features && is_array($item->features)) {
                    $features = array_merge($features, $item->features);
                }
            });

        return array_unique($features);
    }

    /**
     * Get available colors.
     */
    public static function getAvailableColors(): array
    {
        $colors = [];
        
        static::where('is_active', true)
            ->whereNotNull('colors')
            ->get()
            ->each(function ($item) use (&$colors) {
                if ($item->colors && is_array($item->colors)) {
                    $colors = array_merge($colors, $item->colors);
                }
            });

        return array_unique($colors);
    }

    /**
     * Get statistics.
     */
    public static function getStatistics(): array
    {
        return [
            'total_items' => static::count(),
            'active_items' => static::where('is_active', true)->count(),
            'featured_items' => static::where('is_featured', true)->count(),
            'total_views' => static::sum('views_count'),
            'total_likes' => static::sum('likes_count'),
            'by_type' => static::where('is_active', true)
                ->groupBy('type')
                ->selectRaw('type, count(*) as count')
                ->pluck('count', 'type')
                ->toArray(),
            'by_project_type' => static::where('is_active', true)
                ->whereNotNull('project_type')
                ->groupBy('project_type')
                ->selectRaw('project_type, count(*) as count')
                ->pluck('count', 'project_type')
                ->toArray(),
        ];
    }
}
