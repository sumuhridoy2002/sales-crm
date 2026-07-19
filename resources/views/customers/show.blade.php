<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
            <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to customers</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <dl class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $customer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Purchase Count</dt>
                        <dd class="font-medium text-gray-900">{{ $customer->purchase_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Last Purchase</dt>
                        <dd class="font-medium text-gray-900">{{ $customer->last_purchase_at ? $customer->last_purchase_at->format('M d, Y H:i') : 'Never' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Assigned Employee</dt>
                        <dd class="font-medium text-gray-900">{{ $customer->assignedEmployee->name ?? 'Unassigned' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase History</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->sales as $sale)
                            <tr>
                                <td class="px-4 py-3 font-medium">#{{ $sale->id }}</td>
                                <td class="px-4 py-3">{{ $sale->branch->name }}</td>
                                <td class="px-4 py-3">
                                    @foreach($sale->items as $item)
                                        <div>{{ $item->product->name }} x {{ $item->quantity }} @ BDT {{ number_format($item->unit_price, 2) }}</div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">BDT {{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">No purchases recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
