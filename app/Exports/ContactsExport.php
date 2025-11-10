<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Contact::select('id', 'name', 'email', 'country_code', 'phone', 'status', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'الاسم',
            'البريد',
            'الجوال',
            'الحالة',
            'تاريخ الإنشاء'
        ];
    }
}
