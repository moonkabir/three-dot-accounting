@extends('layouts.app')

@section('title', 'Inventory & Stock')
@section('page_heading', 'Inventory & Stock Management')

@section('content')
<div class="space-y-6">
    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Products</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalItems }}</div>
            <div class="text-xs text-slate-500 mt-1">Catalog items tracked</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Low Stock Warnings</span>
            <div class="text-2xl font-black {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-emerald-700' }} mt-1">
                {{ $lowStockCount }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Products below alert threshold</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Stock Valuation</span>
            <div class="text-2xl font-black text-indigo-700 mt-1">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalValuation, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">At purchase cost basis</div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search & Filters Form -->
            <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[220px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <select name="category_id" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <a href="{{ route('inventory.index', array_merge(request()->except('filter'), request('filter') === 'low_stock' ? [] : ['filter' => 'low_stock'])) }}"
                   class="px-3 py-2 rounded-xl text-xs font-medium border transition-colors {{ request('filter') === 'low_stock' ? 'bg-rose-50 border-rose-300 text-rose-700 font-bold' : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                    ⚠️ Low Stock Only
                </a>

                @if (request()->hasAny(['search', 'category_id', 'filter']))
                    <a href="{{ route('inventory.index') }}" class="text-xs text-indigo-600 hover:underline">Reset</a>
                @endif
            </form>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('categoryModal').classList.remove('hidden')" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                    + Category
                </button>
                <a href="{{ route('inventory.create') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Item</span>
                </a>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="overflow-x-auto border-t border-slate-100 pt-3">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] rounded-lg">
                    <tr>
                        <th class="py-3 px-3">Item / SKU</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Stock Level</th>
                        <th class="py-3 px-3">Cost Price</th>
                        <th class="py-3 px-3">Selling Price</th>
                        <th class="py-3 px-3">Margin</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900">{{ $item->name }}</div>
                                <div class="font-mono text-slate-500 text-[11px]">{{ $item->sku }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600 font-medium">
                                {{ $item->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="py-3 px-3">
                                @if ($item->is_low_stock)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        {{ $item->stock_quantity }} {{ $item->unit }} (Low &le; {{ $item->alert_quantity }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $item->stock_quantity }} {{ $item->unit }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-600">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->cost_price, 2) }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->selling_price, 2) }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-emerald-700">
                                +{{ $item->margin_percentage }}%
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                <!-- Stock Adjust Button -->
                                <button type="button"
                                    onclick="openStockAdjustModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock_quantity }}, '{{ $item->unit }}')"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors"
                                    title="Adjust Stock">
                                    Adjust Stock
                                </button>
                                <!-- Edit Button -->
                                <a href="{{ route('inventory.edit', $item) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-xs transition-colors">
                                    Edit
                                </a>
                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('inventory.destroy', $item) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 transition-colors" title="Delete Item">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                No items found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="stockModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-base">Adjust Stock: <span id="modalItemName" class="text-indigo-600"></span></h3>
            <button onclick="document.getElementById('stockModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form id="stockAdjustForm" method="POST" action="">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Current Stock on Hand</label>
                    <div id="modalCurrentStock" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 font-bold text-slate-800"></div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Adjustment Action</label>
                    <select name="adjustment_type" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="add">+ Add Stock (Received stock / Count found extra)</option>
                        <option value="subtract">- Subtract Stock (Damaged / Expired / Lost)</option>
                        <option value="set">= Set Exact Count (After physical audit recount)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Quantity</label>
                    <input type="number" step="0.01" min="0.01" name="quantity" required
                        class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. 5">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reason / Notes</label>
                    <input type="text" name="reason" required
                        class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. Monthly physical inventory audit">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('stockModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">Save Adjustment</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-base">New Category</h3>
            <button onclick="document.getElementById('categoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('inventory.categories.store') }}">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Category Name</label>
                    <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. Dairy & Eggs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="Brief note..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('categoryModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">Create Category</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openStockAdjustModal(itemId, itemName, currentStock, unit) {
        document.getElementById('modalItemName').textContent = itemName;
        document.getElementById('modalCurrentStock').textContent = currentStock + ' ' + unit;
        document.getElementById('stockAdjustForm').action = '/inventory/' + itemId + '/adjust-stock';
        document.getElementById('stockModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection

