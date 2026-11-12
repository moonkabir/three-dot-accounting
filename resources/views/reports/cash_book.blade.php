@extends('layouts.app')

@section('title', 'Cash & Bank Book')
@section('page_heading', 'Cash & Bank Journal')

@section('content')
<div class="space-y-6">
    <!-- Top Filter & Total Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Cash Inflow (+)</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">
                +{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalInflow, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Receipts from customers</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Cash Outflow (-)</span>
            <div class="text-2xl font-black text-rose-600 mt-1">
                -{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalOutflow, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Disbursements to suppliers</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Net Cash Position</span>
            <div class="text-2xl font-black {{ $netBalance >= 0 ? 'text-indigo-700' : 'text-rose-600' }} mt-1">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($netBalance, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Net period cash balance</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Chronological Cash Book</h3>
            <p class="text-xs text-slate-500">Every money-in and money-out transaction</p>
        </div>

        <form method="GET" action="{{ route('reports.cash-book') }}" class="flex flex-wrap items-center gap-3 text-xs">
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
                Filter
            </button>
            <a href="{{ route('reports.cash-book') }}" class="text-indigo-600 hover:underline ml-1">Reset</a>
        </form>
    </div>

    <!-- Journal Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Party (Customer / Vendor)</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Method / Ref</th>
                        <th class="py-3 px-4 text-right">Inflow (+)</th>
                        <th class="py-3 px-4 text-right">Outflow (-)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $txn)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-slate-700 font-medium whitespace-nowrap">{{ $txn->date->format('M d, Y') }}</td>
                            <td class="py-3 px-4">
                                @if ($txn->type === 'in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                                        Receipt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">
                                        Payment
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $txn->party }}</td>
                            <td class="py-3 px-4 text-slate-600">
                                <div>{{ $txn->category }}</div>
                                @if ($txn->notes)
                                    <div class="text-[11px] text-slate-400 italic">{{ $txn->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 capitalize">
                                <div>{{ str_replace('_', ' ', $txn->method) }}</div>
                                @if ($txn->reference)
                                    <div class="font-mono text-slate-400 text-[10px]">{{ $txn->reference }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                @if ($txn->inflow > 0)
                                    +{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($txn->inflow, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-rose-600">
                                @if ($txn->outflow > 0)
                                    -{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($txn->outflow, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">No cash transactions in this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

