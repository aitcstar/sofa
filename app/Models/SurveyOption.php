<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_question_id',
        'label_ar',
        'label_en',
        'value_ar',
        'value_en',
        'order',
    ];

    public function question()
{
    return $this->belongsTo(SurveyQuestion::class);
}
}
