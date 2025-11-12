<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Exports\ContactsExport;
use Maatwebsite\Excel\Facades\Excel;

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
    /*if ($contact->status === 'new') {
        $contact->update(['status' => 'read']);
    }*/
    return view('admin.contacts.show', compact('contact'));
}
// حذف رسالة
public function destroy(Contact $contact)
{
    $contact->delete();
    return redirect()->route('admin.contacts.index')
                     ->with('success', 'تم حذف الرسالة بنجاح.');
}

public function export()
{
    return Excel::download(new ContactsExport, 'contacts.xlsx');
}
public function updateStatus(Request $request, $id)
{
    $request->validate(['status' => 'required|string']);
    $contact = Contact::findOrFail($id);
    $contact->update(['status' => $request->status]);
    return response()->json(['success' => true]);
}


}

