<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    protected $fillable = [
        'name',
        'company',
        'email',
        'country_code',
        'phone',
        'units',
        'message',
        'status'
    ];
}
