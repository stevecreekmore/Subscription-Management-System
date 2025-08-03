<?php

namespace App\Livewire;

use App\Services\SubscriptionService;
use Livewire\Component;

class UserDashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $subscriptionService = new SubscriptionService();
        $this->stats = $subscriptionService->getUserSubscriptionStats(auth()->user());
    }

    public function render()
    {
        $user = auth()->user();
        $subscriptions = $user->subscriptions()
            ->with(['subscriptionPlan.application'])
            ->latest()
            ->get();

        return view('livewire.user-dashboard', [
            'subscriptions' => $subscriptions
        ])->layout('layouts.app');
    }
}
