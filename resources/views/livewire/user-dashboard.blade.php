<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">My Subscription Dashboard</h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-sm font-medium text-blue-600">Active Subscriptions</h3>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['active_subscriptions'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-sm font-medium text-green-600">Total Spent</h3>
                        <p class="text-2xl font-bold text-green-900">${{ number_format($stats['total_spent'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-sm font-medium text-purple-600">Total Savings</h3>
                        <p class="text-2xl font-bold text-purple-900">${{ number_format($stats['total_savings'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-lg">
                        <h3 class="text-sm font-medium text-orange-600">Monthly Cost</h3>
                        <p class="text-2xl font-bold text-orange-900">${{ number_format($stats['monthly_cost'] ?? 0, 2) }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mb-8">
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('browse-plans') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Browse Plans
                        </a>
                        <a href="{{ route('manage-subscriptions') }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Manage Subscriptions
                        </a>
                    </div>
                </div>

                <!-- Recent Subscriptions -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4">My Subscriptions</h2>

                    @if($subscriptions->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">You don't have any subscriptions yet.</p>
                            <a href="{{ route('browse-plans') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Browse Available Plans
                            </a>
                        </div>
                    @else
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $subscription->subscriptionPlan->application->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $subscription->subscriptionPlan->name }}
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
                                                {{ $subscription->expires_at ? $subscription->expires_at->format('M j, Y') : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($subscription->amount_paid, 2) }}
                                                @if($subscription->discount_amount > 0)
                                                    <span class="text-green-600 text-xs">
                                                        (${{ number_format($subscription->discount_amount, 2) }} saved)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
