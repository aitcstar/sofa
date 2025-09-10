<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderTimelineSection;
use App\Models\OrderTimelineItem;

class OrderTimelineController extends Controller
{
    public function edit()
    {
        $section = OrderTimelineSection::with('items')->first();
        return view('admin.home.order-timeline.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'desc_en'  => 'required|string',
            'desc_ar'  => 'required|string',
            'items.*.title_en' => 'nullable|string|max:255',
            'items.*.title_ar' => 'nullable|string|max:255',
            'items.*.desc_en'  => 'nullable|string',
            'items.*.desc_ar'  => 'nullable|string',
            'items.*.color'    => 'nullable|string|max:50',
        ]);

        $section = OrderTimelineSection::firstOrCreate([], $request->only(['title_en','title_ar','desc_en','desc_ar']));
        $section->update($request->only(['title_en','title_ar','desc_en','desc_ar']));

        $incomingIds = collect($request->items)->pluck('id')->filter()->toArray();
        $section->items()->whereNotIn('id', $incomingIds)->delete();

        foreach ($request->items as $itemData) {
            if (!empty($itemData['id'])) {
                $item = OrderTimelineItem::find($itemData['id']);
                if ($item) $item->update($itemData);
            } else {
                if (!empty($itemData['title_en']) || !empty($itemData['title_ar'])) {
                    $section->items()->create($itemData);
                }
            }
        }

        return redirect()->back()->with('success', 'تم تحديث التايم لاين بنجاح');
    }
}
