@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_heading', 'Shop Overview & Performance')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome & Quick Actions Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-2 border border-indigo-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Active Business Session</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-xs text-indigo-200 mt-1">Here is a real-time snapshot of your shop's inventory, sales, and finances today.</p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('customer-bills.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-semibold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>New Sale Bill</span>
            </a>
            <a href="{{ route('vendor-bills.create') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-semibold text-xs border border-amber-400/30 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span>New Purchase Bill</span>
            </a>
            <a href="{{ route('inventory.create') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-xs border border-slate-700 transition-all">
                + Add Item
            </a>
        </div>
    </div>

    <!-- Core Financial & Operational Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Today's Sales -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Today's Sales</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($todaySales, 2) }}
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $todayBillsCount }} {{ Str::plural('invoice', $todayBillsCount) }} created today</span>
                <span class="font-medium text-indigo-600"><a href="{{ route('customer-bills.index') }}">View &rarr;</a></span>
            </div>
        </div>

        <!-- 2. Month's Sales & Gross Profit -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">This Month Sales</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-700">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($monthSales, 2) }}
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>Est. Profit: <strong class="text-emerald-600">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($monthProfit, 2) }}</strong></span>
                <span class="font-medium text-emerald-600"><a href="{{ route('reports.index') }}">Report &rarr;</a></span>
            </div>
        </div>

        <!-- 3. Customer Receivables (Total Due) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Customer Due (Receivable)</span>
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-blue-700">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerReceivables, 2) }}
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $customersCount }} registered customers</span>
                <span class="font-medium text-blue-600"><a href="{{ route('customers.index', ['filter' => 'with_due']) }}">Collect &rarr;</a></span>
            </div>
        </div>

        <!-- 4. Vendor Payables (Total Due) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Vendor Due (Payable)</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-700">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorPayables, 2) }}
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $vendorsCount }} suppliers connected</span>
                <span class="font-medium text-amber-600"><a href="{{ route('vendors.index', ['filter' => 'with_due']) }}">Pay &rarr;</a></span>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row: Inventory Valuation & Low Stock Warning -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Stock Valuation -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase">Inventory Value (Cost)</div>
                <div class="text-xl font-bold text-slate-800">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($inventoryValuation, 2) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $totalItemsCount }} active products in catalog</div>
            </div>
        </div>

        <!-- Low Stock Alert Box -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $lowStockCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase">Low Stock Alerts</div>
                <div class="text-xl font-bold {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    {{ $lowStockCount }} {{ Str::plural('item', $lowStockCount) }} low
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    @if ($lowStockCount > 0)
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-rose-600 font-semibold hover:underline">Reorder products &rarr;</a>
                    @else
                        All items above threshold
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's Cash Flow -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase">Today's Net Cash Flow</div>
                <div class="text-xl font-bold {{ $netCashFlowToday >= 0 ? 'text-teal-700' : 'text-rose-600' }}">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($netCashFlowToday, 2) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    +{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($todayCashIn, 2) }} in / -{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($todayCashOut, 2) }} out
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items Warning Table (if any) -->
    @if ($lowStockItems->count() > 0)
        <div class="bg-rose-50/60 rounded-2xl p-5 border border-rose-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                    <h3 class="font-bold text-rose-900 text-sm">Low Stock Alert: Restock Needed Immediately</h3>
                </div>
                <a href="{{ route('vendor-bills.create') }}" class="text-xs font-semibold text-rose-700 bg-white border border-rose-300 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition-colors">
                    + Create Purchase Bill
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-rose-800 uppercase tracking-wider text-[11px] border-b border-rose-200/60">
                        <tr>
                            <th class="py-2 px-3">Item / SKU</th>
                            <th class="py-2 px-3">Category</th>
                            <th class="py-2 px-3">Current Stock</th>
                            <th class="py-2 px-3">Alert Level</th>
                            <th class="py-2 px-3">Unit Cost</th>
                            <th class="py-2 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-200/40 text-slate-800">
                        @foreach ($lowStockItems as $lItem)
                            <tr class="hover:bg-rose-100/50">
                                <td class="py-2.5 px-3 font-semibold text-slate-900">
                                    {{ $lItem->name }}
                                    <span class="text-xs font-mono text-slate-500 block">{{ $lItem->sku }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $lItem->category->name ?? 'Uncategorized' }}</td>
                                <td class="py-2.5 px-3 font-bold text-rose-700">
                                    {{ $lItem->stock_quantity }} {{ $lItem->unit }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $lItem->alert_quantity }} {{ $lItem->unit }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($lItem->cost_price, 2) }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="{{ route('inventory.edit', $lItem) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Adjust &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Two-Column Recent Activity Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Recent Sales Bills (Customers) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Recent Customer Bills (Sales)</h3>
                    <p class="text-xs text-slate-500">Latest shop sales transactions</p>
                </div>
                <a href="{{ route('customer-bills.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Bill #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono font-semibold text-slate-900">
                                    <a href="{{ route('customer-bills.show', $sale) }}" class="hover:text-indigo-600">
                                        {{ $sale->bill_number }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-sans">{{ $sale->bill_date->format('M d, Y') }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-800 font-medium">
                                    {{ $sale->customer_name ?? ($sale->customer->name ?? 'Walk-in') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($sale->grand_total, 2) }}</div>
                                    @if ($sale->due_amount > 0)
                                        <div class="text-[11px] text-rose-600 font-medium">Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($sale->due_amount, 2) }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($sale->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wide">Paid</span>
                                    @elseif ($sale->payment_status === 'partial')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">Partial</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wide">Unpaid</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('customer-bills.show', $sale) }}" class="p-1 text-slate-500 hover:text-indigo-600" title="View Bill">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('customer-bills.print', $sale) }}" target="_blank" class="p-1 text-slate-500 hover:text-indigo-600 ml-1" title="Print Invoice">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No customer bills recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Recent Purchase Bills (Vendors) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Recent Vendor Bills (Purchases)</h3>
                    <p class="text-xs text-slate-500">Stock purchases & supplier bills</p>
                </div>
                <a href="{{ route('vendor-bills.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Purchase #</th>
                            <th class="py-3 px-4">Vendor</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentPurchases as $purchase)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono font-semibold text-slate-900">
                                    <a href="{{ route('vendor-bills.show', $purchase) }}" class="hover:text-amber-700">
                                        {{ $purchase->bill_number }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-sans">{{ $purchase->bill_date->format('M d, Y') }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-800 font-medium">
                                    {{ $purchase->vendor->name ?? 'Supplier' }}
                                    @if ($purchase->vendor_reference_no)
                                        <div class="text-[11px] text-slate-400">Ref: {{ $purchase->vendor_reference_no }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($purchase->grand_total, 2) }}</div>
                                    @if ($purchase->due_amount > 0)
                                        <div class="text-[11px] text-amber-700 font-medium">Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($purchase->due_amount, 2) }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($purchase->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wide">Paid</span>
                                    @elseif ($purchase->payment_status === 'partial')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">Partial</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wide">Unpaid</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('vendor-bills.show', $purchase) }}" class="p-1 text-slate-500 hover:text-amber-700" title="View Purchase">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('vendor-bills.print', $purchase) }}" target="_blank" class="p-1 text-slate-500 hover:text-amber-700 ml-1" title="Print Purchase Voucher">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No vendor purchase bills recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

