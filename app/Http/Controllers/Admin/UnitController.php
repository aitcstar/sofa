<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['success' => true]);
    }
}
