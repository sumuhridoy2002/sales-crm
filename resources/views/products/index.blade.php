<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Products') }}</h2>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">
                    Add Product
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-800 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            @foreach($branches as $branch)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $branch->name }} Stock</th>
                            @endforeach
                            @if(Auth::user()->role === 'admin')
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Update Stock</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $product->sku }}</td>
                                <td class="px-4 py-3">{{ $product->name }}</td>
                                <td class="px-4 py-3">BDT {{ number_format($product->price, 2) }}</td>
                                @foreach($branches as $branch)
                                    @php
                                        $stock = $product->branches->firstWhere('id', $branch->id)?->pivot->stock_quantity ?? 0;
                                    @endphp
                                    <td class="px-4 py-3">{{ $stock }}</td>
                                @endforeach
                                @if(Auth::user()->role === 'admin')
                                    <td class="px-4 py-3">
                                        <form action="{{ route('products.stock.update') }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <select name="branch_id" class="text-xs rounded border-gray-300 py-1" required>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="stock_quantity" min="0" value="0" class="w-20 text-xs rounded border-gray-300 py-1" required>
                                            <button type="submit" class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + $branches->count() + (Auth::user()->role === 'admin' ? 1 : 0) }}" class="px-4 py-6 text-center text-gray-500">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
