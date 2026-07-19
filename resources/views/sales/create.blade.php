<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record Sale') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" x-data="saleForm()">
                    @csrf

                    <x-input-error :messages="$errors->get('sale')" class="mb-4" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="branch_id" value="Branch" />
                            <select id="branch_id" name="branch_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Select branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="customer_id" value="Customer" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Select customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-900">Line Items</h3>
                            <button type="button" @click="addItem()" class="text-sm text-blue-600 hover:text-blue-800">Add item</button>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 items-end">
                                <div class="md:col-span-8">
                                    <label class="block text-sm text-gray-700">Product</label>
                                    <select :name="`items[${index}][product_id]`" class="mt-1 block w-full rounded-md border-gray-300" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->sku }} - {{ $product->name }} (BDT {{ number_format($product->price, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm text-gray-700">Quantity</label>
                                    <input type="number" min="1" :name="`items[${index}][quantity]`" x-model="item.quantity" class="mt-1 block w-full rounded-md border-gray-300" required>
                                </div>
                                <div class="md:col-span-1">
                                    <button type="button" @click="removeItem(index)" class="text-sm text-red-600 hover:text-red-800" x-show="items.length > 1">Remove</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Complete Sale</x-primary-button>
                        <a href="{{ route('sales.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function saleForm() {
                return {
                    items: [{ product_id: '', quantity: 1 }],
                    addItem() {
                        this.items.push({ product_id: '', quantity: 1 });
                    },
                    removeItem(index) {
                        this.items.splice(index, 1);
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
