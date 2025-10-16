<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'user_id',
        'session_id',
        'name',
        'email',
        'phone',
        'company',
        'commercial_register',
        'tax_number',
        'responses',
        'calculated_price',
        'price_breakdown',
        'status',
        'contacted_at',
        'converted_lead_id',
        'metadata'
    ];

    protected $casts = [
        'responses' => 'array',
        'price_breakdown' => 'array',
        'calculated_price' => 'decimal:2',
        'contacted_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Relationships
     */
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function convertedLead()
    {
        return $this->belongsTo(Lead::class, 'converted_lead_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'contacted' => 'تم التواصل',
            'converted' => 'تم التحويل',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'contacted' => 'info',
            'converted' => 'success',
            default => 'secondary'
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->calculated_price ? 
            number_format($this->calculated_price, 2) . ' ريال' : 
            'غير محدد';
    }

    public function getClientTypeAttribute(): string
    {
        $clientType = $this->responses['client_type'] ?? null;
        return match($clientType) {
            'company' => 'شركة',
            'individual' => 'فرد',
            default => 'غير محدد'
        };
    }

    public function getProjectTypeAttribute(): string
    {
        $projectType = $this->responses['project_type'] ?? null;
        return match($projectType) {
            'building' => 'عمارة سكنية',
            'compound' => 'مجمع سكني',
            'hotel_apartments' => 'شقق فندقية',
            'villa' => 'فيلا',
            'commercial' => 'مشروع تجاري',
            default => 'غير محدد'
        };
    }

    public function getUnitsCountAttribute(): ?int
    {
        return $this->responses['units_count'] ?? null;
    }

    public function getHasInteriorDesignAttribute(): bool
    {
        return ($this->responses['interior_design'] ?? null) === 'yes';
    }

    public function getNeedsFinishingHelpAttribute(): bool
    {
        return ($this->responses['finishing_help'] ?? null) === 'yes';
    }

    public function getNeedsColorConsultationAttribute(): bool
    {
        return ($this->responses['color_consultation'] ?? null) === 'yes';
    }

    public function getPreferredColorsAttribute(): array
    {
        return $this->responses['preferred_colors'] ?? [];
    }

    /**
     * Methods
     */
    public function markAsContacted(): bool
    {
        return $this->update([
            'status' => 'contacted',
            'contacted_at' => now()
        ]);
    }

    public function convertToLead(array $leadData = []): ?Lead
    {
        if ($this->status === 'converted' && $this->converted_lead_id) {
            return $this->convertedLead;
        }

        try {
            \DB::beginTransaction();

            // Create lead
            $lead = Lead::create(array_merge([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'source' => 'questionnaire',
                'status' => 'new',
                'priority' => $this->calculatePriority(),
                'project_type' => $this->responses['project_type'] ?? null,
                'units_count' => $this->responses['units_count'] ?? null,
                'budget_range' => $this->estimateBudgetRange(),
                'estimated_value' => $this->calculated_price,
                'description' => $this->generateLeadDescription(),
                'next_follow_up_at' => now()->addDay(),
                'metadata' => [
                    'questionnaire_response_id' => $this->id,
                    'questionnaire_id' => $this->questionnaire_id,
                    'responses' => $this->responses,
                    'price_breakdown' => $this->price_breakdown,
                    'commercial_register' => $this->commercial_register,
                    'tax_number' => $this->tax_number,
                ]
            ], $leadData));

            // Update response status
            $this->update([
                'status' => 'converted',
                'converted_lead_id' => $lead->id
            ]);

            // Log lead creation
            $lead->logActivity('created_from_questionnaire', 'تم إنشاء العميل المحتمل من استبيان', auth()->id());

            \DB::commit();
            return $lead;

        } catch (\Exception $e) {
            \DB::rollBack();
            return null;
        }
    }

    private function calculatePriority(): string
    {
        $score = 0;

        // Score based on project type
        $projectType = $this->responses['project_type'] ?? null;
        $score += match($projectType) {
            'compound' => 30,
            'hotel_apartments' => 25,
            'building' => 20,
            'villa' => 15,
            default => 10
        };

        // Score based on units count
        $unitsCount = $this->responses['units_count'] ?? 0;
        if ($unitsCount > 50) {
            $score += 30;
        } elseif ($unitsCount > 20) {
            $score += 20;
        } elseif ($unitsCount > 10) {
            $score += 15;
        } else {
            $score += 10;
        }

        // Score based on additional services
        if ($this->has_interior_design) $score += 10;
        if ($this->needs_finishing_help) $score += 10;
        if ($this->needs_color_consultation) $score += 5;

        // Score based on client type
        if (($this->responses['client_type'] ?? null) === 'company') {
            $score += 15;
        }

        // Score based on calculated price
        if ($this->calculated_price > 1000000) {
            $score += 25;
        } elseif ($this->calculated_price > 500000) {
            $score += 20;
        } elseif ($this->calculated_price > 250000) {
            $score += 15;
        }

        // Determine priority
        if ($score >= 80) {
            return 'urgent';
        } elseif ($score >= 60) {
            return 'high';
        } elseif ($score >= 40) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function estimateBudgetRange(): string
    {
        if (!$this->calculated_price) {
            return 'unknown';
        }

        $price = $this->calculated_price;

        if ($price < 50000) {
            return 'under_50k';
        } elseif ($price < 100000) {
            return '50k_100k';
        } elseif ($price < 250000) {
            return '100k_250k';
        } elseif ($price < 500000) {
            return '250k_500k';
        } elseif ($price < 1000000) {
            return '500k_1m';
        } else {
            return 'over_1m';
        }
    }

    private function generateLeadDescription(): string
    {
        $description = "عميل محتمل من الاستبيان:\n\n";
        
        $description .= "نوع المشروع: {$this->project_type}\n";
        
        if ($this->units_count) {
            $description .= "عدد الوحدات: {$this->units_count}\n";
        }
        
        $description .= "نوع العميل: {$this->client_type}\n";
        
        if ($this->calculated_price) {
            $description .= "السعر المقدر: {$this->formatted_price}\n";
        }

        $services = [];
        if ($this->has_interior_design) $services[] = 'تصميم داخلي';
        if ($this->needs_finishing_help) $services[] = 'مساعدة في التشطيب';
        if ($this->needs_color_consultation) $services[] = 'استشارة ألوان';
        
        if (!empty($services)) {
            $description .= "الخدمات المطلوبة: " . implode(', ', $services) . "\n";
        }

        if (!empty($this->preferred_colors)) {
            $description .= "الألوان المفضلة: " . implode(', ', $this->preferred_colors) . "\n";
        }

        if ($this->company) {
            $description .= "الشركة: {$this->company}\n";
        }

        if ($this->commercial_register) {
            $description .= "السجل التجاري: {$this->commercial_register}\n";
        }

        if ($this->tax_number) {
            $description .= "الرقم الضريبي: {$this->tax_number}\n";
        }

        return $description;
    }

    public function getAnswerText(string $questionId): string
    {
        $answer = $this->responses[$questionId] ?? null;
        
        if ($answer === null) {
            return 'لم يتم الإجابة';
        }

        // Get question from questionnaire
        $question = collect($this->questionnaire->questions)
            ->firstWhere('id', $questionId);

        if (!$question) {
            return $answer;
        }

        return $this->formatAnswerForQuestion($question, $answer);
    }

    private function formatAnswerForQuestion(array $question, $answer): string
    {
        switch ($question['type']) {
            case 'radio':
            case 'select':
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
     * Get summary of responses.
     */
    public function getSummary(): array
    {
        return [
            'client_info' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'client_type' => $this->client_type,
            ],
            'project_info' => [
                'project_type' => $this->project_type,
                'units_count' => $this->units_count,
                'has_interior_design' => $this->has_interior_design,
                'needs_finishing_help' => $this->needs_finishing_help,
                'needs_color_consultation' => $this->needs_color_consultation,
                'preferred_colors' => $this->preferred_colors,
            ],
            'pricing' => [
                'calculated_price' => $this->calculated_price,
                'formatted_price' => $this->formatted_price,
                'price_breakdown' => $this->price_breakdown,
            ],
            'business_info' => [
                'commercial_register' => $this->commercial_register,
                'tax_number' => $this->tax_number,
            ],
            'status' => [
                'status' => $this->status,
                'status_text' => $this->status_text,
                'contacted_at' => $this->contacted_at,
                'converted_lead_id' => $this->converted_lead_id,
            ]
        ];
    }

    /**
     * Generate unique session ID for anonymous users.
     */
    public static function generateSessionId(): string
    {
        return 'qr_' . uniqid() . '_' . time();
    }

    /**
     * Get statistics for responses.
     */
    public static function getStatistics(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        return [
            'total_responses' => static::whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending_responses' => static::pending()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'contacted_responses' => static::contacted()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'converted_responses' => static::converted()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'conversion_rate' => static::getConversionRate($startDate, $endDate),
            'average_price' => static::whereBetween('created_at', [$startDate, $endDate])->avg('calculated_price') ?? 0,
            'total_value' => static::whereBetween('created_at', [$startDate, $endDate])->sum('calculated_price') ?? 0,
            'by_project_type' => static::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(responses, "$.project_type")) as project_type, COUNT(*) as count')
                ->groupBy('project_type')
                ->pluck('count', 'project_type')
                ->toArray(),
            'by_client_type' => static::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(responses, "$.client_type")) as client_type, COUNT(*) as count')
                ->groupBy('client_type')
                ->pluck('count', 'client_type')
                ->toArray(),
        ];
    }

    /**
     * Get conversion rate.
     */
    public static function getConversionRate(?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): float
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();
        $converted = $query->where('status', 'converted')->count();

        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }
}
