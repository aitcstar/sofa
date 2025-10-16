<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'questions',
        'pricing_rules',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'questions' => 'array',
        'pricing_rules' => 'array',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Relationships
     */
    public function responses()
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    public function pendingResponses()
    {
        return $this->hasMany(QuestionnaireResponse::class)
                    ->where('status', 'pending');
    }

    public function contactedResponses()
    {
        return $this->hasMany(QuestionnaireResponse::class)
                    ->where('status', 'contacted');
    }

    public function convertedResponses()
    {
        return $this->hasMany(QuestionnaireResponse::class)
                    ->where('status', 'converted');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Accessors
     */
    public function getResponsesCountAttribute(): int
    {
        return $this->responses()->count();
    }

    public function getConversionRateAttribute(): float
    {
        $total = $this->responses()->count();
        $converted = $this->convertedResponses()->count();
        
        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    /**
     * Methods
     */
    public function calculatePrice(array $responses): array
    {
        $basePrice = 0;
        $breakdown = [];
        $rules = $this->pricing_rules ?? [];

        foreach ($this->questions as $question) {
            $questionId = $question['id'];
            $userResponse = $responses[$questionId] ?? null;

            if (!$userResponse) {
                continue;
            }

            // Apply pricing rules for this question
            if (isset($rules[$questionId])) {
                $rule = $rules[$questionId];
                $price = $this->applyPricingRule($rule, $userResponse, $responses);
                
                if ($price > 0) {
                    $basePrice += $price;
                    $breakdown[] = [
                        'question' => $question['text'],
                        'answer' => $this->formatAnswer($question, $userResponse),
                        'price' => $price
                    ];
                }
            }
        }

        // Apply global modifiers
        if (isset($rules['global_modifiers'])) {
            foreach ($rules['global_modifiers'] as $modifier) {
                $modifierPrice = $this->applyGlobalModifier($modifier, $responses, $basePrice);
                if ($modifierPrice != 0) {
                    $basePrice += $modifierPrice;
                    $breakdown[] = [
                        'question' => $modifier['description'],
                        'answer' => '',
                        'price' => $modifierPrice
                    ];
                }
            }
        }

        return [
            'total_price' => max(0, $basePrice),
            'breakdown' => $breakdown,
            'currency' => 'SAR'
        ];
    }

    private function applyPricingRule(array $rule, $userResponse, array $allResponses): float
    {
        $price = 0;

        switch ($rule['type']) {
            case 'fixed':
                if (isset($rule['options'][$userResponse])) {
                    $price = $rule['options'][$userResponse];
                }
                break;

            case 'multiplier':
                if (is_numeric($userResponse)) {
                    $price = $userResponse * ($rule['rate'] ?? 0);
                }
                break;

            case 'range':
                if (is_numeric($userResponse)) {
                    foreach ($rule['ranges'] as $range) {
                        if ($userResponse >= $range['min'] && $userResponse <= $range['max']) {
                            $price = $range['price'];
                            break;
                        }
                    }
                }
                break;

            case 'conditional':
                if ($this->evaluateCondition($rule['condition'], $allResponses)) {
                    $price = $rule['price'];
                }
                break;

            case 'percentage':
                if (isset($rule['base_question'])) {
                    $baseValue = $allResponses[$rule['base_question']] ?? 0;
                    if (is_numeric($baseValue)) {
                        $price = $baseValue * ($rule['percentage'] / 100);
                    }
                }
                break;
        }

        return $price;
    }

    private function applyGlobalModifier(array $modifier, array $responses, float $basePrice): float
    {
        $modifierPrice = 0;

        switch ($modifier['type']) {
            case 'percentage_discount':
                if ($this->evaluateCondition($modifier['condition'], $responses)) {
                    $modifierPrice = -($basePrice * ($modifier['percentage'] / 100));
                }
                break;

            case 'fixed_discount':
                if ($this->evaluateCondition($modifier['condition'], $responses)) {
                    $modifierPrice = -$modifier['amount'];
                }
                break;

            case 'percentage_increase':
                if ($this->evaluateCondition($modifier['condition'], $responses)) {
                    $modifierPrice = $basePrice * ($modifier['percentage'] / 100);
                }
                break;

            case 'fixed_increase':
                if ($this->evaluateCondition($modifier['condition'], $responses)) {
                    $modifierPrice = $modifier['amount'];
                }
                break;
        }

        return $modifierPrice;
    }

    private function evaluateCondition(array $condition, array $responses): bool
    {
        $questionId = $condition['question'];
        $operator = $condition['operator'];
        $value = $condition['value'];
        $userResponse = $responses[$questionId] ?? null;

        if ($userResponse === null) {
            return false;
        }

        switch ($operator) {
            case 'equals':
                return $userResponse == $value;
            case 'not_equals':
                return $userResponse != $value;
            case 'greater_than':
                return is_numeric($userResponse) && $userResponse > $value;
            case 'less_than':
                return is_numeric($userResponse) && $userResponse < $value;
            case 'greater_equal':
                return is_numeric($userResponse) && $userResponse >= $value;
            case 'less_equal':
                return is_numeric($userResponse) && $userResponse <= $value;
            case 'contains':
                return is_array($userResponse) && in_array($value, $userResponse);
            case 'not_contains':
                return is_array($userResponse) && !in_array($value, $userResponse);
            default:
                return false;
        }
    }

    private function formatAnswer(array $question, $answer): string
    {
        switch ($question['type']) {
            case 'select':
            case 'radio':
                $option = collect($question['options'])->firstWhere('value', $answer);
                return $option['label'] ?? $answer;

            case 'checkbox':
                if (is_array($answer)) {
                    $labels = [];
                    foreach ($answer as $value) {
                        $option = collect($question['options'])->firstWhere('value', $value);
                        $labels[] = $option['label'] ?? $value;
                    }
                    return implode(', ', $labels);
                }
                return $answer;

            case 'number':
                return number_format($answer);

            default:
                return $answer;
        }
    }

    /**
     * Get default questionnaire structure.
     */
    public static function getDefaultQuestionnaire(): array
    {
        return [
            'title' => 'استبيان تقدير المشروع',
            'description' => 'يرجى الإجابة على الأسئلة التالية للحصول على تقدير دقيق لمشروعكم',
            'questions' => [
                [
                    'id' => 'units_count',
                    'text' => 'كم عدد الوحدات في المشروع؟',
                    'type' => 'number',
                    'required' => true,
                    'min' => 1,
                    'max' => 1000
                ],
                [
                    'id' => 'client_type',
                    'text' => 'نوع العميل',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'company', 'label' => 'شركة'],
                        ['value' => 'individual', 'label' => 'فرد']
                    ]
                ],
                [
                    'id' => 'project_type',
                    'text' => 'نوع المشروع',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'building', 'label' => 'عمارة سكنية'],
                        ['value' => 'compound', 'label' => 'مجمع سكني'],
                        ['value' => 'hotel_apartments', 'label' => 'شقق فندقية'],
                        ['value' => 'villa', 'label' => 'فيلا']
                    ]
                ],
                [
                    'id' => 'interior_design',
                    'text' => 'هل يوجد تصميم داخلي؟',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'yes', 'label' => 'نعم'],
                        ['value' => 'no', 'label' => 'لا']
                    ]
                ],
                [
                    'id' => 'finishing_help',
                    'text' => 'هل تحتاج مساعدة في التشطيب؟',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'yes', 'label' => 'نعم'],
                        ['value' => 'no', 'label' => 'لا']
                    ]
                ],
                [
                    'id' => 'color_consultation',
                    'text' => 'هل تحتاج مساعدة في اختيار الألوان؟',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'yes', 'label' => 'نعم'],
                        ['value' => 'no', 'label' => 'لا']
                    ]
                ],
                [
                    'id' => 'preferred_colors',
                    'text' => 'الألوان المطلوبة',
                    'type' => 'checkbox',
                    'required' => false,
                    'options' => [
                        ['value' => 'white', 'label' => 'أبيض'],
                        ['value' => 'beige', 'label' => 'بيج'],
                        ['value' => 'gray', 'label' => 'رمادي'],
                        ['value' => 'brown', 'label' => 'بني'],
                        ['value' => 'blue', 'label' => 'أزرق'],
                        ['value' => 'green', 'label' => 'أخضر'],
                        ['value' => 'custom', 'label' => 'ألوان مخصصة']
                    ]
                ]
            ],
            'pricing_rules' => [
                'units_count' => [
                    'type' => 'multiplier',
                    'rate' => 50000 // 50,000 SAR per unit base price
                ],
                'project_type' => [
                    'type' => 'fixed',
                    'options' => [
                        'building' => 100000,
                        'compound' => 150000,
                        'hotel_apartments' => 120000,
                        'villa' => 80000
                    ]
                ],
                'interior_design' => [
                    'type' => 'conditional',
                    'condition' => [
                        'question' => 'interior_design',
                        'operator' => 'equals',
                        'value' => 'yes'
                    ],
                    'price' => 25000
                ],
                'finishing_help' => [
                    'type' => 'conditional',
                    'condition' => [
                        'question' => 'finishing_help',
                        'operator' => 'equals',
                        'value' => 'yes'
                    ],
                    'price' => 15000
                ],
                'color_consultation' => [
                    'type' => 'conditional',
                    'condition' => [
                        'question' => 'color_consultation',
                        'operator' => 'equals',
                        'value' => 'yes'
                    ],
                    'price' => 5000
                ],
                'global_modifiers' => [
                    [
                        'type' => 'percentage_discount',
                        'description' => 'خصم للشركات',
                        'condition' => [
                            'question' => 'client_type',
                            'operator' => 'equals',
                            'value' => 'company'
                        ],
                        'percentage' => 10
                    ]
                ]
            ]
        ];
    }

    /**
     * Create default questionnaire.
     */
    public static function createDefault(): self
    {
        $data = static::getDefaultQuestionnaire();
        
        return static::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'questions' => $data['questions'],
            'pricing_rules' => $data['pricing_rules'],
            'is_active' => true,
            'sort_order' => 1
        ]);
    }

    /**
     * Get statistics.
     */
    public function getStatistics(): array
    {
        $responses = $this->responses();
        
        return [
            'total_responses' => $responses->count(),
            'pending_responses' => $this->pendingResponses()->count(),
            'contacted_responses' => $this->contactedResponses()->count(),
            'converted_responses' => $this->convertedResponses()->count(),
            'conversion_rate' => $this->conversion_rate,
            'average_price' => $responses->avg('calculated_price') ?? 0,
            'total_value' => $responses->sum('calculated_price') ?? 0,
            'responses_by_day' => $responses
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->pluck('count', 'date')
                ->toArray()
        ];
    }

    /**
     * Validate questionnaire structure.
     */
    public function validateStructure(): array
    {
        $errors = [];

        if (empty($this->questions) || !is_array($this->questions)) {
            $errors[] = 'يجب أن يحتوي الاستبيان على أسئلة';
            return $errors;
        }

        foreach ($this->questions as $index => $question) {
            $questionErrors = $this->validateQuestion($question, $index);
            $errors = array_merge($errors, $questionErrors);
        }

        return $errors;
    }

    private function validateQuestion(array $question, int $index): array
    {
        $errors = [];
        $questionNum = $index + 1;

        if (empty($question['id'])) {
            $errors[] = "السؤال رقم {$questionNum}: مطلوب معرف السؤال";
        }

        if (empty($question['text'])) {
            $errors[] = "السؤال رقم {$questionNum}: مطلوب نص السؤال";
        }

        if (empty($question['type'])) {
            $errors[] = "السؤال رقم {$questionNum}: مطلوب نوع السؤال";
        } elseif (!in_array($question['type'], ['text', 'number', 'email', 'radio', 'checkbox', 'select', 'textarea'])) {
            $errors[] = "السؤال رقم {$questionNum}: نوع السؤال غير صحيح";
        }

        if (in_array($question['type'], ['radio', 'checkbox', 'select'])) {
            if (empty($question['options']) || !is_array($question['options'])) {
                $errors[] = "السؤال رقم {$questionNum}: مطلوب خيارات للسؤال";
            } else {
                foreach ($question['options'] as $optionIndex => $option) {
                    if (empty($option['value']) || empty($option['label'])) {
                        $errors[] = "السؤال رقم {$questionNum}, الخيار رقم " . ($optionIndex + 1) . ": مطلوب قيمة وتسمية للخيار";
                    }
                }
            }
        }

        return $errors;
    }
}
