<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        // Overall system statistics
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'total_applications' => Application::count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')->sum('amount_paid'),
            'total_revenue' => Subscription::sum('amount_paid'),
            'total_discounts_given' => Subscription::sum('discount_amount'),
        ];

        // Recent subscriptions
        $recentSubscriptions = Subscription::with(['user', 'subscriptionPlan.application'])
            ->latest()
            ->limit(10)
            ->get();

        // Company statistics
        $companyStats = Company::withCount(['users'])
            ->with(['users' => function ($query) {
                $query->withCount(['subscriptions as active_subscriptions_count' => function ($q) {
                    $q->where('status', 'active');
                }]);
            }])
            ->get()
            ->map(function ($company) {
                return [
                    'name' => $company->name,
                    'users_count' => $company->users_count,
                    'total_subscriptions' => $company->users->sum('active_subscriptions_count'),
                    'total_revenue' => $company->users->sum(function ($user) {
                        return $user->subscriptions->sum('amount_paid');
                    }),
                ];
            });

        // Application statistics
        $applicationStats = Application::withCount(['subscriptionPlans'])
            ->with(['subscriptionPlans.subscriptions'])
            ->get()
            ->map(function ($application) {
                $totalSubscriptions = $application->subscriptionPlans->sum(function ($plan) {
                    return $plan->subscriptions->count();
                });
                $activeSubscriptions = $application->subscriptionPlans->sum(function ($plan) {
                    return $plan->subscriptions->where('status', 'active')->count();
                });
                $revenue = $application->subscriptionPlans->sum(function ($plan) {
                    return $plan->subscriptions->sum('amount_paid');
                });

                return [
                    'name' => $application->name,
                    'plans_count' => $application->subscription_plans_count,
                    'total_subscriptions' => $totalSubscriptions,
                    'active_subscriptions' => $activeSubscriptions,
                    'revenue' => $revenue,
                ];
            });

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'recentSubscriptions' => $recentSubscriptions,
            'companyStats' => $companyStats,
            'applicationStats' => $applicationStats,
        ])->layout('layouts.app');
    }
}
