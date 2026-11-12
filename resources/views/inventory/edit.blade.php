@extends('layouts.app')

@section('title', 'Edit ' . $item->name)
@section('page_heading', 'Edit Inventory Product')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Edit Product: {{ $item->name }}</h2>
                <p class="text-xs text-slate-500">Update pricing, categories and alert levels</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Back to Catalog
            </a>
        </div>

        <form method="POST" action="{{ route('inventory.update', $item) }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block font-semibold text-slate-700 mb-1">SKU / Item Code *</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $item->sku) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 font-mono text-xs uppercase">
                </div>

                <div>
                    <label for="category_id" class="block font-semibold text-slate-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="name" class="block font-semibold text-slate-700 mb-1">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="unit" class="block font-semibold text-slate-700 mb-1">Unit of Measure *</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $item->unit) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label for="cost_price" class="block font-semibold text-slate-700 mb-1">Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', $item->cost_price) }}" required
                            class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>

                <div>
                    <label for="selling_price" class="block font-semibold text-slate-700 mb-1">Selling Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" id="selling_price" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}" required
                            class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Current Stock Quantity</label>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold">
                        {{ $item->stock_quantity }} {{ $item->unit }}
                        <span class="text-[11px] font-normal text-slate-400 ml-1">(Adjust via "Adjust Stock" on catalog page)</span>
                    </div>
                </div>

                <div>
                    <label for="alert_quantity" class="block font-semibold text-slate-700 mb-1">Low Stock Alert Level</label>
                    <input type="number" step="0.01" min="0" id="alert_quantity" name="alert_quantity" value="{{ old('alert_quantity', $item->alert_quantity) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block font-semibold text-slate-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="active" {{ old('status', $item->status) === 'active' ? 'selected' : '' }}>Active (Available for Sale)</option>
                        <option value="inactive" {{ old('status', $item->status) === 'inactive' ? 'selected' : '' }}>Inactive (Archived)</option>
                    </select>
                </div>
                <div>
                    <label for="description" class="block font-semibold text-slate-700 mb-1">Notes</label>
                    <input type="text" id="description" name="description" value="{{ old('description', $item->description) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/30">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

