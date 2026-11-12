@extends('layouts.app')

@section('title', 'Add Inventory Item')
@section('page_heading', 'Add New Inventory Product')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Product Details</h2>
                <p class="text-xs text-slate-500">Add a product to your inventory catalog</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Back to Catalog
            </a>
        </div>

        <form method="POST" action="{{ route('inventory.store') }}" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block font-semibold text-slate-700 mb-1">SKU / Item Code *</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $suggestedSku) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 font-mono text-xs uppercase" placeholder="e.g. BEV-001">
                </div>

                <div>
                    <label for="category_id" class="block font-semibold text-slate-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="name" class="block font-semibold text-slate-700 mb-1">Product / Item Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. Pure Orange Juice (1L)">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="unit" class="block font-semibold text-slate-700 mb-1">Unit of Measure *</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="pcs, kg, box, bottle">
                </div>

                <div>
                    <label for="cost_price" class="block font-semibold text-slate-700 mb-1">Purchase / Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', '0.00') }}" required
                            class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>

                <div>
                    <label for="selling_price" class="block font-semibold text-slate-700 mb-1">Selling Price (Retail) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" id="selling_price" name="selling_price" value="{{ old('selling_price', '0.00') }}" required
                            class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="stock_quantity" class="block font-semibold text-slate-700 mb-1">Initial Opening Stock *</label>
                    <input type="number" step="0.01" min="0" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', '0') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-bold text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Stock on shelf today</p>
                </div>

                <div>
                    <label for="alert_quantity" class="block font-semibold text-slate-700 mb-1">Low Stock Alert Threshold</label>
                    <input type="number" step="0.01" min="0" id="alert_quantity" name="alert_quantity" value="{{ old('alert_quantity', '5') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                    <p class="text-[11px] text-slate-400 mt-1">Alert when stock falls to or below this</p>
                </div>
            </div>

            <div>
                <label for="description" class="block font-semibold text-slate-700 mb-1">Product Description / Notes</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="Optional notes or supplier info...">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/30">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

