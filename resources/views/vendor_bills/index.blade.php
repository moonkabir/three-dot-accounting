@extends('layouts.app')

@section('title', 'Vendor Purchase Bills')
@section('page_heading', 'Vendor Purchase Bills & Expenses')

@section('content')
<div class="space-y-6">
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Purchases Billed</span>
            <div class="text-2xl font-black text-slate-900 mt-1">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPurchasesAmount, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Total inventory purchases</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Paid to Vendors</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPaidAmount, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Cleared bills to suppliers</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Vendor Payable</span>
            <div class="text-2xl font-black text-amber-700 mt-1">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalDueAmount, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Unpaid purchase balances</div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Search & Filters -->
            <form method="GET" action="{{ route('vendor-bills.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search bill # or vendor..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <select name="payment_status" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">All Payment Statuses</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid Only</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial Only</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                </select>

                <select name="vendor_id" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none max-w-[180px]">
                    <option value="">All Vendors</option>
                    @foreach ($vendors as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>

                @if (request()->hasAny(['search', 'payment_status', 'vendor_id']))
                    <a href="{{ route('vendor-bills.index') }}" class="text-xs text-amber-700 hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('vendor-bills.create') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-md shadow-amber-600/20 flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Create Purchase Bill</span>
            </a>
        </div>

        <!-- Bills Table -->
        <div class="overflow-x-auto border-t border-slate-100 pt-3">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">Purchase Bill #</th>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Vendor / Supplier</th>
                        <th class="py-3 px-3">Grand Total</th>
                        <th class="py-3 px-3">Paid Amount</th>
                        <th class="py-3 px-3">Due Balance</th>
                        <th class="py-3 px-3">Payment Status</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bills as $bill)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                <a href="{{ route('vendor-bills.show', $bill) }}" class="hover:text-amber-700">
                                    {{ $bill->bill_number }}
                                </a>
                                @if ($bill->vendor_reference_no)
                                    <div class="text-[11px] text-slate-400 font-sans font-normal">Ref: {{ $bill->vendor_reference_no }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $bill->bill_date->format('M d, Y') }}</td>
                            <td class="py-3 px-3 font-medium text-slate-800">
                                <a href="{{ route('vendors.show', $bill->vendor) }}" class="hover:text-amber-700">
                                    {{ $bill->vendor->name }}
                                </a>
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->grand_total, 2) }}
                            </td>
                            <td class="py-3 px-3 font-medium text-emerald-700">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->paid_amount, 2) }}
                            </td>
                            <td class="py-3 px-3 font-bold {{ $bill->due_amount > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->due_amount, 2) }}
                            </td>
                            <td class="py-3 px-3">
                                @if ($bill->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Paid</span>
                                @elseif ($bill->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">Partial</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('vendor-bills.show', $bill) }}" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-medium text-xs">
                                    View
                                </a>
                                <a href="{{ route('vendor-bills.print', $bill) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs">
                                    Print
                                </a>
                                <form method="POST" action="{{ route('vendor-bills.destroy', $bill) }}" class="inline" onsubmit="return confirm('Void/delete this purchase bill? Stock added by this bill will be subtracted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600" title="Delete & Deduct Stock">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">No vendor purchase bills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $bills->links() }}
        </div>
    </div>
</div>
@endsection

