@extends('layouts.app')

@section('title', 'Financial Reports')
@section('page_heading', 'Profit & Loss and Accounting Reports')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Accounting Period</h2>
            <p class="text-xs text-slate-500">Filter sales, purchases, and profit by date</p>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="flex items-center gap-2">
                <label for="start_date" class="font-medium text-slate-600">From:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                    class="p-2 rounded-xl border border-slate-300 text-xs">
            </div>

            <div class="flex items-center gap-2">
                <label for="end_date" class="font-medium text-slate-600">To:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                    class="p-2 rounded-xl border border-slate-300 text-xs">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition-all">
                Filter Report
            </button>
            <a href="{{ route('reports.index') }}" class="text-indigo-600 hover:underline ml-1">Reset</a>
        </form>
    </div>

    <!-- Profit & Loss Summary Card -->
    <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-indigo-800/60">
            <div>
                <span class="text-xs uppercase tracking-wider text-indigo-300 font-semibold">Income Statement</span>
                <h3 class="text-2xl font-black tracking-tight mt-0.5">Estimated Gross Profit</h3>
                <p class="text-xs text-indigo-200 mt-1">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="text-right">
                <div class="text-3xl sm:text-4xl font-black {{ $grossProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($grossProfit, 2) }}
                </div>
                <div class="text-xs text-indigo-300 mt-1">
                    Margin: <span class="font-bold text-white">{{ $profitMargin }}%</span> of sales revenue
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 text-xs">
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <span class="text-indigo-300 font-medium">1. Total Sales Revenue (+)</span>
                <div class="text-xl font-bold text-white mt-1">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalSales, 2) }}</div>
                <div class="text-[11px] text-indigo-300 mt-1">{{ $salesCount }} sales invoices issued</div>
            </div>

            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <span class="text-indigo-300 font-medium">2. Cost of Goods Sold (-)</span>
                <div class="text-xl font-bold text-rose-300 mt-1">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($cogs, 2) }}</div>
                <div class="text-[11px] text-indigo-300 mt-1">Base cost of inventory sold</div>
            </div>

            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <span class="text-indigo-300 font-medium">3. Net Cash Flow</span>
                <div class="text-xl font-bold {{ $netCashFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }} mt-1">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($netCashFlow, 2) }}
                </div>
                <div class="text-[11px] text-indigo-300 mt-1">Cash In: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($cashIn, 2) }} | Out: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($cashOut, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Comparative Overview: Sales vs Purchases -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Overview -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Customer Sales Breakdown</h3>
                <span class="text-xs font-semibold text-indigo-600"><a href="{{ route('customer-bills.index') }}">View Bills &rarr;</a></span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-600">Total Invoiced Sales</span>
                    <span class="font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalSales, 2) }}</span>
                </div>
                <div class="flex justify-between p-3 rounded-xl bg-emerald-50/60 border border-emerald-100 text-emerald-800">
                    <span class="font-medium">Collected / Paid Amount</span>
                    <span class="font-bold">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalSalesPaid, 2) }}</span>
                </div>
                <div class="flex justify-between p-3 rounded-xl bg-rose-50/60 border border-rose-100 text-rose-800">
                    <span class="font-medium">Customer Due Balance</span>
                    <span class="font-bold">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalSalesDue, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 pt-2 px-1 text-[11px]">
                    <span>All-Time Total Receivables (Across all periods):</span>
                    <strong class="text-slate-800">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalCustomerReceivables, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Purchases Overview -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Vendor Purchases Breakdown</h3>
                <span class="text-xs font-semibold text-amber-700"><a href="{{ route('vendor-bills.index') }}">View Bills &rarr;</a></span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-600">Total Restock Purchases</span>
                    <span class="font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPurchases, 2) }}</span>
                </div>
                <div class="flex justify-between p-3 rounded-xl bg-emerald-50/60 border border-emerald-100 text-emerald-800">
                    <span class="font-medium">Paid to Suppliers</span>
                    <span class="font-bold">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPurchasesPaid, 2) }}</span>
                </div>
                <div class="flex justify-between p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-amber-900">
                    <span class="font-medium">Vendor Payable Due</span>
                    <span class="font-bold">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPurchasesDue, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 pt-2 px-1 text-[11px]">
                    <span>All-Time Total Payables (Across all periods):</span>
                    <strong class="text-slate-800">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalVendorPayables, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Top Revenue Items in Period</h3>
                <p class="text-xs text-slate-500">Highest grossing inventory products</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Inventory Catalog &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Rank</th>
                        <th class="py-3 px-4">Item Name</th>
                        <th class="py-3 px-4 text-center">Units Sold</th>
                        <th class="py-3 px-4 text-right">Total Revenue Generated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topSellingItems as $index => $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-slate-400 font-mono font-bold">#{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->item_name }}</td>
                            <td class="py-3 px-4 text-center font-medium text-slate-700">{{ $item->total_qty }}</td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->total_revenue, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">No items sold in selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

