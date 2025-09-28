<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\Exhibition;
class GalleryDetailsController extends Controller
{
    /**
     * عرض صفحة تفاصيل المعرض
     */
    public function show($id)
    {
        $seo = SeoSetting::where('page','gallery')->first();

        $exhibition = Exhibition::with([
            'images',
            'packages.units.items',
            'packages.images',  // نضيف علاقة الصور هنا
            'steps'
        ])->findOrFail($id);
        $firstPackage = $exhibition->packages->first();

        $project = [
            'name_ar' => $exhibition?->name_ar ?? '',
            'name_en' => $exhibition?->name_en ?? '',
            'colors' => $exhibition->packages?->units->flatMap(fn($unit) => $unit->items)->pluck('color_ar')->implode(', ') ?? '',
            'delivery_date' => $exhibition->delivery_date ? \Carbon\Carbon::parse($exhibition->delivery_date)->format('F Y') : '—',
            'type_ar' => $exhibition->category?->name_ar ?? '',
            'type_en' => $exhibition->category?->name_en ?? '',
            'area_ar' => $exhibition->packages?->description_ar ?? '',
            'area_en' => $exhibition->packages?->description_en ?? '',
            'kitchen_ar' => $exhibition->packages?->description_ar ?? '',
            'kitchen_en' => $exhibition->packages?->description_en ?? '',
            'pieces_count' => $exhibition->packages?->units->flatMap(fn($unit) => $unit->items)->count() ?? 0,
            'tv_design' => 'تصميم خشبي كلاسيكي',
            'images' => $exhibition->images->pluck('image')->toArray() ?? [],
            'steps' => $exhibition->steps->map(fn($step) => [
                'icon' => $step->icon ?? '',
                'title_ar' => $step->title_ar ?? '',
                'title_en' => $step->title_en ?? ''
            ])->toArray() ?? [],
// صور الباكج نفسها
'details_images' => $firstPackage->images->toArray(),






            'steps_images' => [],
            'packages' => $exhibition->packages?->units->map(fn($unit) => [
                'icon' => $unit->icon ?? '',
                'title_ar' => $unit->name_ar ?? '',
                'title_en' => $unit->name_en ?? '',

                'items' => $unit->items->map(fn($item) => [
                    'name_ar' => $item->item_name_ar ?? '',
                    'name_en' => $item->item_name_en ?? '',
                    'size' => $item->dimensions ?? '',
                    'material_ar' => $item->material_ar ?? '',
                    'material_en' => $item->material_en ?? '',
                    'color_ar' => $item->color_ar ?? '',
                    'color_en' => $item->color_en ?? '',
                    'color_code' => $item->background_color ?? '',
                    // هنا بدل 'image_path' لو الصورة الرئيسية موجودة
                    'image' => $item->image_path ?? '',
                    'quantity' => $item->quantity ?? 1,
                ])->toArray()
            ])->toArray() ?? []
        ];

        $pageData = [
            'title' => $exhibition->title,
            'description' => $exhibition->summary,
            'project' => $project
        ];

       // dd( $project);

        return view('frontend.pages.gallery-details', compact('seo','pageData'));
    }


}
