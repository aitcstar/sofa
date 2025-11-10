<?php

namespace App\Exports;

use App\Models\HelpRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HelpRequestsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return HelpRequest::orderBy('created_at', 'desc')->get([
            'id',
            'name',
            'company',
            'email',
            'country_code',
            'phone',
            'units',
            'message',
            'created_at'
        ]);
    }

    public function headings(): array
    {
        return [
            '#',
            'الاسم',
            'الشركة',
            'البريد الإلكتروني',
            'الهاتف',
            'عدد الوحدات',
            'الرسالة',
            'تاريخ الإرسال'
        ];
    }
}
