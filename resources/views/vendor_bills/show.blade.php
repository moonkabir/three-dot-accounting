@extends('layouts.app')

@section('title', 'Purchase Bill ' . $vendorBill->bill_number)
@section('page_heading', 'Purchase Bill #' . $vendorBill->bill_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('vendor-bills.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            &larr; Back to Purchases
        </a>

        <div class="flex items-center gap-2">
            @if ($vendorBill->due_amount > 0)
                <button type="button" onclick="document.getElementById('billPaymentModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-sm flex items-center gap-1.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Pay Bill</span>
                </button>
            @endif

            <a href="{{ route('vendor-bills.print', $vendorBill) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs shadow-sm flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Voucher</span>
            </a>
        </div>
    </div>

    <!-- Purchase Bill Sheet -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm space-y-6">
        <!-- Sheet Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <div class="inline-flex w-10 h-10 rounded-xl bg-amber-600 text-white font-black items-center justify-center text-lg mb-2">3•</div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">{{ $settings['shop_name'] ?? 'Three Dot Mart' }}</h1>
                <p class="text-xs text-slate-500">Purchase Order / Goods Received Voucher</p>
                <p class="text-xs text-slate-400 mt-1">Shop: {{ $settings['shop_address'] ?? '' }}</p>
            </div>

            <div class="sm:text-right">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2
                    {{ $vendorBill->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($vendorBill->payment_status === 'partial' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                    Payment: {{ $vendorBill->payment_status }}
                </div>
                <div class="font-mono font-bold text-slate-900 text-base">{{ $vendorBill->bill_number }}</div>
                @if ($vendorBill->vendor_reference_no)
                    <div class="text-xs font-mono text-slate-600">Supplier Inv: {{ $vendorBill->vendor_reference_no }}</div>
                @endif
                <div class="text-xs text-slate-500 mt-1">Date: {{ $vendorBill->bill_date->format('F d, Y') }}</div>
                @if ($vendorBill->due_date)
                    <div class="text-xs text-slate-400">Due: {{ $vendorBill->due_date->format('F d, Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Vendor Info Box -->
        <div class="bg-amber-50/60 rounded-xl p-4 border border-amber-200/60 text-xs">
            <span class="font-semibold text-amber-800 uppercase tracking-wider text-[10px] block mb-1">Supplier / Vendor</span>
            <div class="font-bold text-slate-900 text-sm">
                <a href="{{ route('vendors.show', $vendorBill->vendor) }}" class="text-amber-900 hover:underline">
                    {{ $vendorBill->vendor->name }}
                </a>
                @if ($vendorBill->vendor->company_name)
                    <span class="text-slate-500 font-normal">({{ $vendorBill->vendor->company_name }})</span>
                @endif
            </div>
            @if ($vendorBill->vendor->phone)
                <div class="text-slate-600 mt-0.5">Phone: {{ $vendorBill->vendor->phone }}</div>
            @endif
            @if ($vendorBill->vendor->address)
                <div class="text-slate-500">{{ $vendorBill->vendor->address }}</div>
            @endif
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-y border-slate-100">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Item / Product</th>
                        <th class="py-3 px-4 text-right">Purchase Cost</th>
                        <th class="py-3 px-4 text-center">Qty Restocked</th>
                        <th class="py-3 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($vendorBill->items as $index => $item)
                        <tr>
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $item->item_name }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-slate-800">+{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Balance Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-4 border-t border-slate-100 gap-6 text-xs">
            <div class="max-w-xs space-y-2 text-slate-500">
                @if ($vendorBill->notes)
                    <div>
                        <span class="font-semibold text-slate-700 block">Note:</span>
                        <p class="text-[11px] italic">{{ $vendorBill->notes }}</p>
                    </div>
                @endif
                <div>
                    <span class="font-semibold text-slate-700 block">Payment Mode:</span>
                    <span class="capitalize text-slate-800">{{ str_replace('_', ' ', $vendorBill->payment_method) }}</span>
                </div>
            </div>

            <div class="w-full sm:w-72 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-semibold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->subtotal, 2) }}</span>
                </div>
                @if ($vendorBill->discount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount (-)</span>
                        <span>-{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->discount, 2) }}</span>
                    </div>
                @endif
                @if ($vendorBill->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Freight / Tax (+)</span>
                        <span>+{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->tax, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-100">
                    <span>Grand Total</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>Amount Paid</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-amber-800 font-bold p-2 rounded-lg bg-amber-50 border border-amber-200">
                    <span>Balance Due</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment History on This Bill -->
        @if ($vendorBill->payments->count() > 0)
            <div class="pt-6 border-t border-slate-100">
                <h3 class="font-bold text-slate-800 text-xs mb-3">Payments Made Against This Bill</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                            <tr>
                                <th class="py-2 px-3">Date</th>
                                <th class="py-2 px-3">Method</th>
                                <th class="py-2 px-3">Ref / Cheque</th>
                                <th class="py-2 px-3">Amount</th>
                                <th class="py-2 px-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($vendorBill->payments as $p)
                                <tr>
                                    <td class="py-2 px-3 font-medium text-slate-800">{{ $p->payment_date->format('M d, Y') }}</td>
                                    <td class="py-2 px-3 capitalize">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-500">{{ $p->reference_number ?? '—' }}</td>
                                    <td class="py-2 px-3 font-bold text-amber-800">-{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($p->amount, 2) }}</td>
                                    <td class="py-2 px-3 text-slate-400 text-[11px]">{{ $p->notes ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Record Bill Payment Modal -->
@if ($vendorBill->due_amount > 0)
<div id="billPaymentModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Make Payment to Supplier</h3>
                <p class="text-xs text-slate-500">Bill: {{ $vendorBill->bill_number }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendorBill->due_amount, 2) }})</p>
            </div>
            <button onclick="document.getElementById('billPaymentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('vendor-bills.payments.store', $vendorBill) }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label for="pay_amount" class="block font-semibold text-slate-700 mb-1">Amount to Pay *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                    <input type="number" step="0.01" min="0.01" max="{{ $vendorBill->due_amount }}" id="pay_amount" name="amount" value="{{ $vendorBill->due_amount }}" required
                        class="w-full pl-7 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs font-bold text-amber-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="p_date" class="block font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" id="p_date" name="payment_date" value="{{ date('Y-m-d') }}" required
                        class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label for="p_method" class="block font-semibold text-slate-700 mb-1">Payment Mode *</label>
                    <select id="p_method" name="payment_method" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="cash">Cash Out</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cheque">Bank Cheque</option>
                        <option value="mobile">Mobile Banking</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="p_ref" class="block font-semibold text-slate-700 mb-1">Cheque / Trx Ref #</label>
                <input type="text" id="p_ref" name="reference_number" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs" placeholder="e.g. CHQ-0029">
            </div>

            <div>
                <label for="p_notes" class="block font-semibold text-slate-700 mb-1">Notes</label>
                <input type="text" id="p_notes" name="notes" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs" placeholder="Payment remarks...">
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('billPaymentModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-md shadow-amber-600/30">
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

