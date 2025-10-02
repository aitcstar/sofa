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
            'package.packageUnitItems.unit',
            'package.packageUnitItems.item',
            'package.images',
            'steps'
        ])->findOrFail($id);

        $package = $exhibition->package; // افتراض أن كل معرض مرتبط بباكج واحد

        $project = [
            'name_ar' => $exhibition->name_ar ?? '',
            'name_en' => $exhibition->name_en ?? '',
            'colors' => $package?->packageUnitItems->pluck('item.color_ar')->implode(', ') ?? '',
            'delivery_date' => $exhibition->delivery_date
                ? \Carbon\Carbon::parse($exhibition->delivery_date)->format('F Y')
                : '—',
            'type_ar' => $exhibition->category?->name_ar ?? '',
            'type_en' => $exhibition->category?->name_en ?? '',
            'area_ar' => $package?->description_ar ?? '',
            'area_en' => $package?->description_en ?? '',
            'kitchen_ar' => $package?->description_ar ?? '',
            'kitchen_en' => $package?->description_en ?? '',
            'pieces_count' => $package?->packageUnitItems->count() ?? 0,
            'tv_design' => 'تصميم خشبي كلاسيكي',
            'images' => $exhibition->images->pluck('image')->toArray() ?? [],
            'steps' => $exhibition->steps->map(fn($step) => [
                'icon' => $step->icon ?? '',
                'title_ar' => $step->title_ar ?? '',
                'title_en' => $step->title_en ?? ''
            ])->toArray() ?? [],
            'details_images' => $package?->images->map(fn($img) => [
                'image_path' => $img->image_path
            ])->toArray() ?? [],
            'steps_images' => [],
            'packages' => $package?->packageUnitItems->map(fn($pui) => [
                'unit_id' => $pui->unit->id,
                'title_ar' => $pui->unit->name_ar ?? '',
                'title_en' => $pui->unit->name_en ?? '',
                'name_ar' => $pui->item->item_name_ar ?? '',
                'name_en' => $pui->item->item_name_en ?? '',
                'size' => $pui->item->dimensions ?? '',
                'material_ar' => $pui->item->material_ar ?? '',
                'material_en' => $pui->item->material_en ?? '',
                'color_ar' => $pui->item->color_ar ?? '',
                'color_en' => $pui->item->color_en ?? '',
                'color_code' => $pui->item->background_color ?? '',
                'image' => $pui->item->image_path ?? '',
                'quantity' => $pui->item->quantity ?? 1,
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
