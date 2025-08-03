<?php

namespace App\Services;

use App\Models\DiscountRule;
use App\Models\LearningDependency;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function subscribe(User $user, SubscriptionPlan $plan): array
    {
        return DB::transaction(function () use ($user, $plan) {
            // Check learning dependencies
            $learningCheck = $this->checkLearningDependencies($user, $plan);
            if (!$learningCheck['allowed']) {
                return [
                    'success' => false,
                    'message' => 'Learning requirements not met: ' . $learningCheck['message'],
                    'missing_courses' => $learningCheck['missing_courses'] ?? []
                ];
            }

            // Calculate discount
            $discount = $this->calculateDiscount($user, $plan->price);
            $finalPrice = $plan->price - $discount;

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => $this->calculateExpiryDate($plan),
                'amount_paid' => $finalPrice,
                'discount_amount' => $discount,
                'metadata' => [
                    'application' => $plan->application->name,
                    'plan_name' => $plan->name,
                    'original_price' => $plan->price,
                ]
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
                'discount_applied' => $discount,
                'final_price' => $finalPrice,
                'message' => 'Subscription created successfully!'
            ];
        });
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        if ($subscription->status === 'cancelled') {
            return false;
        }

        $subscription->update([
            'status' => 'cancelled',
            'metadata' => array_merge($subscription->metadata ?? [], [
                'cancelled_at' => now()->toISOString(),
                'cancelled_by' => auth()->id(),
            ])
        ]);

        return true;
    }

    public function renewSubscription(Subscription $subscription): array
    {
        if ($subscription->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Only active subscriptions can be renewed'
            ];
        }

        $plan = $subscription->subscriptionPlan;
        $user = $subscription->user;

        // Calculate new discount
        $discount = $this->calculateDiscount($user, $plan->price);
        $finalPrice = $plan->price - $discount;

        // Update subscription
        $subscription->update([
            'expires_at' => $this->calculateExpiryDate($plan, $subscription->expires_at),
            'amount_paid' => $subscription->amount_paid + $finalPrice,
            'discount_amount' => $subscription->discount_amount + $discount,
            'metadata' => array_merge($subscription->metadata ?? [], [
                'last_renewed' => now()->toISOString(),
                'renewal_price' => $finalPrice,
            ])
        ]);

        return [
            'success' => true,
            'subscription' => $subscription,
            'discount_applied' => $discount,
            'final_price' => $finalPrice,
            'message' => 'Subscription renewed successfully!'
        ];
    }

    protected function checkLearningDependencies(User $user, SubscriptionPlan $plan): array
    {
        $dependencies = $plan->learningDependencies()->where('is_required', true)->get();
        
        if ($dependencies->isEmpty()) {
            return ['allowed' => true];
        }

        $missingCourses = [];
        foreach ($dependencies as $dependency) {
            if (!$dependency->checkUserCompletion($user)) {
                $missingCourses[] = [
                    'course_id' => $dependency->required_course_id,
                    'learning_system_url' => $dependency->learning_system_url,
                ];
            }
        }

        if (!empty($missingCourses)) {
            return [
                'allowed' => false,
                'message' => 'Please complete required training courses before subscribing',
                'missing_courses' => $missingCourses
            ];
        }

        return ['allowed' => true];
    }

    protected function calculateDiscount(User $user, float $originalPrice): float
    {
        $discountRules = DiscountRule::where('is_active', true)->get();
        $maxDiscount = 0;

        foreach ($discountRules as $rule) {
            $discount = $rule->calculateDiscount($user, $originalPrice);
            $maxDiscount = max($maxDiscount, $discount);
        }

        return $maxDiscount;
    }

    protected function calculateExpiryDate(SubscriptionPlan $plan, $fromDate = null): ?\Carbon\Carbon
    {
        $startDate = $fromDate ? \Carbon\Carbon::parse($fromDate) : now();

        return match($plan->billing_period) {
            'monthly' => $startDate->addMonth(),
            'yearly' => $startDate->addYear(),
            'one-time' => null,
            default => null,
        };
    }

    public function getUserSubscriptionStats(User $user): array
    {
        $subscriptions = $user->subscriptions()->with('subscriptionPlan.application')->get();
        
        return [
            'total_subscriptions' => $subscriptions->count(),
            'active_subscriptions' => $subscriptions->where('status', 'active')->count(),
            'cancelled_subscriptions' => $subscriptions->where('status', 'cancelled')->count(),
            'expired_subscriptions' => $subscriptions->where('status', 'expired')->count(),
            'total_spent' => $subscriptions->sum('amount_paid'),
            'total_savings' => $subscriptions->sum('discount_amount'),
            'monthly_cost' => $subscriptions->where('status', 'active')
                ->filter(fn($sub) => $sub->subscriptionPlan->billing_period === 'monthly')
                ->sum(fn($sub) => $sub->subscriptionPlan->price),
            'yearly_cost' => $subscriptions->where('status', 'active')
                ->filter(fn($sub) => $sub->subscriptionPlan->billing_period === 'yearly')
                ->sum(fn($sub) => $sub->subscriptionPlan->price),
        ];
    }
}