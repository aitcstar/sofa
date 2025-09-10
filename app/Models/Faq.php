<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_ar',
        'category_en',
        'question_ar',
        'question_en',
        'answer_ar',
        'answer_en',
        'sort',
        'page',
    ];


    // في موديل Faq.php
public function getQuestionAttribute()
{
    return app()->getLocale() == 'ar' ? $this->question_ar : $this->question_en;
}

public function getAnswerAttribute()
{
    return app()->getLocale() == 'ar' ? $this->answer_ar : $this->answer_en;
}

}


