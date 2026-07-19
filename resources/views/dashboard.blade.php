<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Business Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-800 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Welcome, {{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-600">Role: <strong class="uppercase text-indigo-600">{{ Auth::user()->role }}</strong></p>
                @if(Auth::user()->role === 'employee')
                    <div class="mt-4 p-4 bg-indigo-50 rounded-md inline-block">
                        <span class="text-sm font-semibold text-indigo-800">Your KPI Score: </span>
                        <span class="text-2xl font-bold text-indigo-600">{{ Auth::user()->kpi_score }}</span>
                    </div>
                @endif
            </div>

            @if(Auth::user()->role === 'admin')
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold text-red-600 mb-2">CRM: Inactive Customers</h3>
                    <p class="text-sm text-gray-500 mb-4">Customers with no purchase in the last {{ $inactiveDays }} days.</p>

                    @if($inactiveCustomers->isEmpty())
                        <p class="text-gray-500">No inactive customers found right now.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchases</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Purchase</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Employee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    @foreach($inactiveCustomers as $customer)
                                        <tr>
                                            <td class="px-6 py-4 font-medium text-gray-900">{{ $customer->name }} ({{ $customer->email }})</td>
                                            <td class="px-6 py-4 text-gray-500">{{ $customer->purchase_count }}</td>
                                            <td class="px-6 py-4 text-gray-500">{{ $customer->last_purchase_at ? $customer->last_purchase_at->diffForHumans() : 'Never' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                                    {{ $customer->assignedEmployee->name ?? 'Unassigned' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
                                                    <form action="{{ route('crm.customers.assign', $customer) }}" method="POST" class="flex items-center gap-2">
                                                        @csrf
                                                        <select name="employee_id" class="text-xs rounded border-gray-300 py-1" required>
                                                            <option value="">Select Employee</option>
                                                            @foreach($employees as $emp)
                                                                <option value="{{ $emp->id }}" {{ $customer->assigned_employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 shadow-sm transition">
                                                            Assign
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('crm.customers.re-engage', $customer) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 text-white text-xs px-3 py-1 rounded hover:bg-green-700 shadow-sm transition">
                                                            Send Promo
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
