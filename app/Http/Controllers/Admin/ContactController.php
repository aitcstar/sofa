<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;

class ContactController extends Controller
{
// عرض كل الرسائل
public function index()
{
    $messages = Contact::latest()->paginate(10);
    return view('admin.contacts.index', compact('messages'));
}
// عرض تفاصيل رسالة
public function show(Contact $contact)
{
    if ($contact->status === 'new') {
        $contact->update(['status' => 'read']);
    }
    return view('admin.contacts.show', compact('contact'));
}
// حذف رسالة
public function destroy(Contact $contact)
{
    $contact->delete();
    return redirect()->route('admin.contacts.index')
                     ->with('success', 'تم حذف الرسالة بنجاح.');
}
}

