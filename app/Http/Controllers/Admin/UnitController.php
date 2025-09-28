<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitImage;
class UnitController extends Controller
{
   /* public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['success' => true]);
    }*/

    public function destroy($unitId, $imageId)
    {
        $image = UnitImage::findOrFail($imageId);

        // حذف الصورة من التخزين
        if (\Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
}
