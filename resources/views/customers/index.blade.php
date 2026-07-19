<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Customers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchases</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Purchase</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            @php
                                $inactiveDays = config('crm.inactive_days', 90);
                                $isInactive = $customer->last_purchase_at === null || $customer->last_purchase_at->lt(now()->subDays($inactiveDays));
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-gray-500">{{ $customer->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $customer->purchase_count }}</td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $customer->last_purchase_at ? $customer->last_purchase_at->format('M d, Y') : 'Never' }}
                                </td>
                                <td class="px-4 py-3">{{ $customer->assignedEmployee->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $isInactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $isInactive ? 'Inactive' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">View history</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
