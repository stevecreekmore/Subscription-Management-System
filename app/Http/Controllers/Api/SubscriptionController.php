<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function getUserSubscriptions(Request $request, $userId)
    {
        $application = $this->getApplicationFromToken($request);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $subscriptions = $user->subscriptions()
            ->with(['subscriptionPlan' => function ($query) use ($application) {
                $query->where('application_id', $application->id);
            }])
            ->whereHas('subscriptionPlan', function ($query) use ($application) {
                $query->where('application_id', $application->id);
            })
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan' => [
                        'id' => $subscription->subscriptionPlan->id,
                        'name' => $subscription->subscriptionPlan->name,
                        'slug' => $subscription->subscriptionPlan->slug,
                        'price' => $subscription->subscriptionPlan->price,
                        'billing_period' => $subscription->subscriptionPlan->billing_period,
                    ],
                    'status' => $subscription->status,
                    'started_at' => $subscription->started_at,
                    'expires_at' => $subscription->expires_at,
                    'amount_paid' => $subscription->amount_paid,
                    'is_active' => $subscription->isActive(),
                ];
            });

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions
        ]);
    }

    public function checkUserAccess(Request $request, $userId, $planId)
    {
        $application = $this->getApplicationFromToken($request);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $plan = SubscriptionPlan::where('id', $planId)
            ->where('application_id', $application->id)
            ->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found'], 404);
        }

        $hasAccess = $user->subscriptions()
            ->where('subscription_plan_id', $planId)
            ->where('status', 'active')
            ->exists();

        return response()->json([
            'success' => true,
            'has_access' => $hasAccess,
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
            ]
        ]);
    }

    public function getAvailablePlans(Request $request)
    {
        $application = $this->getApplicationFromToken($request);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $plans = $application->subscriptionPlans()
            ->where('is_active', true)
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'billing_period' => $plan->billing_period,
                    'features' => $plan->features,
                ];
            });

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    public function createSubscription(Request $request)
    {
        $application = $this->getApplicationFromToken($request);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = User::find($request->user_id);
        $plan = SubscriptionPlan::where('id', $request->plan_id)
            ->where('application_id', $application->id)
            ->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found for this application'], 404);
        }

        $result = $this->subscriptionService->subscribe($user, $plan);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'subscription' => [
                    'id' => $result['subscription']->id,
                    'status' => $result['subscription']->status,
                    'started_at' => $result['subscription']->started_at,
                    'expires_at' => $result['subscription']->expires_at,
                    'amount_paid' => $result['subscription']->amount_paid,
                    'discount_amount' => $result['subscription']->discount_amount,
                ],
                'message' => $result['message']
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'missing_courses' => $result['missing_courses'] ?? null
            ], 400);
        }
    }

    public function getApplicationStats(Request $request)
    {
        $application = $this->getApplicationFromToken($request);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $totalSubscriptions = Subscription::whereHas('subscriptionPlan', function ($query) use ($application) {
            $query->where('application_id', $application->id);
        })->count();

        $activeSubscriptions = Subscription::whereHas('subscriptionPlan', function ($query) use ($application) {
            $query->where('application_id', $application->id);
        })->where('status', 'active')->count();

        $totalRevenue = Subscription::whereHas('subscriptionPlan', function ($query) use ($application) {
            $query->where('application_id', $application->id);
        })->sum('amount_paid');

        return response()->json([
            'success' => true,
            'stats' => [
                'total_subscriptions' => $totalSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
                'total_revenue' => $totalRevenue,
                'total_plans' => $application->subscriptionPlans()->count(),
            ]
        ]);
    }

    protected function getApplicationFromToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = base64_decode($token);
            [$applicationId, $timestamp] = explode(':', $decoded);
            
            // Token expires after 24 hours
            if ((time() - $timestamp) > 86400) {
                return null;
            }

            return Application::where('id', $applicationId)
                ->where('is_active', true)
                ->first();

        } catch (\Exception $e) {
            return null;
        }
    }
}
