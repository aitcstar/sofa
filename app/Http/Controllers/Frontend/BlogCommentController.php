<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogComment;

class BlogCommentController extends Controller
{
    public function store(Request $request, Blog $blog)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'pending', // يبدأ معلق لحين موافقة الأدمن
        ]);

        return redirect()->back()->with('success', 'تم إرسال تعليقك بانتظار المراجعة.');
    }
}
