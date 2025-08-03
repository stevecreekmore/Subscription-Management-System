<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class LearningDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_plan_id',
        'required_course_id',
        'learning_system_url',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function checkUserCompletion(User $user): bool
    {
        try {
            $response = Http::get($this->learning_system_url . '/api/user-completion', [
                'user_id' => $user->id,
                'course_id' => $this->required_course_id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['completed'] ?? false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
