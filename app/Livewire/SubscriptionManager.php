<?php

namespace App\Livewire;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionManager extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $cancellingSubscription = null;

    public function cancelSubscription($subscriptionId)
    {
        $subscription = Subscription::where('id', $subscriptionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $subscriptionService = new SubscriptionService();
        
        if ($subscriptionService->cancelSubscription($subscription)) {
            session()->flash('success', 'Subscription cancelled successfully.');
        } else {
            session()->flash('error', 'Unable to cancel subscription. It may already be cancelled.');
        }
        
        $this->cancellingSubscription = null;
    }

    public function renewSubscription($subscriptionId)
    {
        $subscription = Subscription::where('id', $subscriptionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $subscriptionService = new SubscriptionService();
        $result = $subscriptionService->renewSubscription($subscription);
        
        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render()
    {
        $subscriptions = auth()->user()
            ->subscriptions()
            ->with(['subscriptionPlan.application'])
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.subscription-manager', [
            'subscriptions' => $subscriptions
        ])->layout('layouts.app');
    }
}
