<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    /**
     * الموافقة على تعليق
     */
    public function approve($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();

        return redirect()->back()->with('success', 'تم قبول التعليق بنجاح.');
    }

    /**
     * رفض تعليق
     */
    public function reject($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();

        return redirect()->back()->with('success', 'تم رفض التعليق.');
    }
}
