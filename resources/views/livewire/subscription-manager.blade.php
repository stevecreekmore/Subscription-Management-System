<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Manage My Subscriptions</h1>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filters -->
                <div class="mb-6">
                    <label for="status-filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                    <select wire:model.live="statusFilter" id="status-filter"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md max-w-xs">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- Subscriptions Table -->
                @if($subscriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $subscription->subscriptionPlan->application->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                <div class="font-medium">{{ $subscription->subscriptionPlan->name }}</div>
                                                <div class="text-xs text-gray-400">${{ number_format($subscription->subscriptionPlan->price, 2) }}/{{ $subscription->subscriptionPlan->billing_period }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($subscription->status === 'active') bg-green-100 text-green-800
                                                @elseif($subscription->status === 'cancelled') bg-red-100 text-red-800
                                                @elseif($subscription->status === 'expired') bg-gray-100 text-gray-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->started_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($subscription->expires_at)
                                                {{ $subscription->expires_at->format('M j, Y') }}
                                                @if($subscription->expires_at->isPast())
                                                    <span class="text-red-500 text-xs">(Expired)</span>
                                                @elseif($subscription->expires_at->diffInDays() <= 7)
                                                    <span class="text-orange-500 text-xs">(Expires Soon)</span>
                                                @endif
                                            @else
                                                Never
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${{ number_format($subscription->amount_paid, 2) }}
                                            @if($subscription->discount_amount > 0)
                                                <div class="text-green-600 text-xs">
                                                    ${{ number_format($subscription->discount_amount, 2) }} saved
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if($subscription->status === 'active')
                                                @if($subscription->subscriptionPlan->billing_period !== 'one-time')
                                                    <button wire:click="renewSubscription({{ $subscription->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="renewSubscription({{ $subscription->id }})"
                                                            class="text-blue-600 hover:text-blue-900 disabled:opacity-50">
                                                        <span wire:loading.remove wire:target="renewSubscription({{ $subscription->id }})">Renew</span>
                                                        <span wire:loading wire:target="renewSubscription({{ $subscription->id }})">...</span>
                                                    </button>
                                                @endif

                                                <button wire:click="$set('cancellingSubscription', {{ $subscription->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                    Cancel
                                                </button>
                                            @elseif($subscription->status === 'expired' && $subscription->subscriptionPlan->billing_period !== 'one-time')
                                                <button wire:click="renewSubscription({{ $subscription->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="renewSubscription({{ $subscription->id }})"
                                                        class="text-green-600 hover:text-green-900 disabled:opacity-50">
                                                    <span wire:loading.remove wire:target="renewSubscription({{ $subscription->id }})">Reactivate</span>
                                                    <span wire:loading wire:target="renewSubscription({{ $subscription->id }})">...</span>
                                                </button>
                                            @else
                                                <span class="text-gray-400">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $subscriptions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg mb-4">No subscriptions found.</p>
                        <a href="{{ route('browse-plans') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Browse Available Plans
                        </a>
                    </div>
                @endif

                <!-- Cancel Confirmation Modal -->
                @if($cancellingSubscription)
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Subscription</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to cancel this subscription? This action cannot be undone.
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button wire:click="cancelSubscription({{ $cancellingSubscription }})"
                                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                        Yes, Cancel
                                    </button>
                                    <button wire:click="$set('cancellingSubscription', null)"
                                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        No, Keep
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
