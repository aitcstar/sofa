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
        'status',
        'project_size',
        'client_type',
        'has_interior_plan',
        'needs_finishing_help',
        'needs_color_help',
    ];
}
