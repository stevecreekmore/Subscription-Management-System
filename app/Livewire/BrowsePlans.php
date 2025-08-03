<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Livewire\Component;

class BrowsePlans extends Component
{
    public $selectedApplication = '';
    public $subscribingToPlan = null;
    public $subscriptionResult = null;

    public function subscribe($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $subscriptionService = new SubscriptionService();
        
        $this->subscriptionResult = $subscriptionService->subscribe(auth()->user(), $plan);
        
        if ($this->subscriptionResult['success']) {
            session()->flash('success', $this->subscriptionResult['message']);
            $this->dispatch('subscription-created');
        } else {
            session()->flash('error', $this->subscriptionResult['message']);
        }
    }

    public function render()
    {
        $applications = Application::where('is_active', true)->with('subscriptionPlans')->get();
        
        $plans = SubscriptionPlan::query()
            ->with(['application'])
            ->where('is_active', true)
            ->when($this->selectedApplication, function ($query) {
                $query->whereHas('application', function ($q) {
                    $q->where('slug', $this->selectedApplication);
                });
            })
            ->get()
            ->groupBy('application.name');

        return view('livewire.browse-plans', [
            'applications' => $applications,
            'plansByApplication' => $plans
        ])->layout('layouts.app');
    }
}
