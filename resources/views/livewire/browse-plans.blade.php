<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Browse Subscription Plans</h1>

                <!-- Application Filter -->
                <div class="mb-6">
                    <label for="application-filter" class="block text-sm font-medium text-gray-700">Filter by Application</label>
                    <select wire:model.live="selectedApplication" id="application-filter" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Applications</option>
                        @foreach($applications as $app)
                            <option value="{{ $app->slug }}">{{ $app->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                        @if($subscriptionResult && !$subscriptionResult['success'] && isset($subscriptionResult['missing_courses']))
                            <div class="mt-2">
                                <p class="font-semibold">Missing required courses:</p>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach($subscriptionResult['missing_courses'] as $course)
                                        <li>
                                            Course ID: {{ $course['course_id'] }} 
                                            <a href="{{ $course['learning_system_url'] }}" target="_blank" class="text-blue-600 underline">
                                                (Complete Course)
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Plans by Application -->
                @foreach($plansByApplication as $applicationName => $plans)
                    <div class="mb-12">
                        <h2 class="text-xl font-bold mb-6 text-gray-800">{{ $applicationName }}</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($plans as $plan)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                        <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                                        
                                        <div class="mb-4">
                                            <span class="text-3xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                            <span class="text-gray-500">/ {{ $plan->billing_period }}</span>
                                        </div>

                                        @if($plan->features && count($plan->features) > 0)
                                            <ul class="mb-6 space-y-2">
                                                @foreach($plan->features as $feature)
                                                    <li class="flex items-center text-sm text-gray-600">
                                                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @php
                                            $userHasThisPlan = auth()->user()->subscriptions()
                                                ->where('subscription_plan_id', $plan->id)
                                                ->where('status', 'active')
                                                ->exists();
                                        @endphp

                                        @if($userHasThisPlan)
                                            <button disabled class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                                                Already Subscribed
                                            </button>
                                        @else
                                            <button wire:click="subscribe({{ $plan->id }})" 
                                                    wire:loading.attr="disabled"
                                                    wire:target="subscribe({{ $plan->id }})"
                                                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors disabled:opacity-50">
                                                <span wire:loading.remove wire:target="subscribe({{ $plan->id }})">
                                                    Subscribe Now
                                                </span>
                                                <span wire:loading wire:target="subscribe({{ $plan->id }})">
                                                    Processing...
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($plansByApplication->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No subscription plans available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
