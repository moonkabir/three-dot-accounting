@extends('layouts.app')

@section('title', 'Ledger - ' . $vendor->name)
@section('page_heading', 'Vendor Ledger & Supplier Statement')

@section('content')
<div class="space-y-6">
    <!-- Top Vendor Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 text-white font-black text-xl flex items-center justify-center shadow-md">
                    {{ substr($vendor->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $vendor->name }}</h2>
                    @if ($vendor->company_name)
                        <div class="text-xs font-semibold text-amber-800">{{ $vendor->company_name }}</div>
                    @endif
                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                        @if ($vendor->phone)
                            <span>📞 {{ $vendor->phone }}</span>
                        @endif
                        @if ($vendor->email)
                            <span>✉️ {{ $vendor->email }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($vendor->address)
                <p class="text-xs text-slate-500 mt-2 pl-1">📍 {{ $vendor->address }}</p>
            @endif
        </div>

        <!-- Balance Metrics & Action Buttons -->
        <div class="flex flex-wrap items-center gap-4">
            <div class="text-right px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Outstanding Payable</div>
                <div class="text-2xl font-black {{ $vendor->current_balance > 0 ? 'text-amber-800' : ($vendor->current_balance < 0 ? 'text-blue-600' : 'text-emerald-700') }}">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendor->current_balance, 2) }}
                </div>
                <div class="text-[10px] text-slate-400">
                    {{ $vendor->current_balance > 0 ? 'Owed to Supplier' : ($vendor->current_balance < 0 ? 'Advance Paid' : 'Account Settled') }}
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <button type="button" onclick="document.getElementById('vendorPaymentModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-md shadow-amber-600/20 flex items-center gap-1.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Pay Vendor</span>
                </button>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('vendor-bills.create') }}?vendor_id={{ $vendor->id }}" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 font-medium text-xs border border-amber-200 text-center flex-1">
                        + Purchase Bill
                    </a>
                    <a href="{{ route('vendors.edit', $vendor) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium text-xs text-center">
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Opening Balance</span>
            <div class="text-lg font-bold text-slate-800 mt-0.5">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendor->opening_balance, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Purchases Billed</span>
            <div class="text-lg font-bold text-amber-800 mt-0.5">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPurchases, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Paid to Vendor</span>
            <div class="text-lg font-bold text-emerald-700 mt-0.5">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPaid, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Purchase Bills Count</span>
            <div class="text-lg font-bold text-slate-800 mt-0.5">
                {{ $vendor->bills->count() }} bills
            </div>
        </div>
    </div>

    <!-- Vendor Purchase Bills Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Vendor Purchase Bills</h3>
                <p class="text-xs text-slate-500">Inventory shipments and bills from {{ $vendor->name }}</p>
            </div>
            <a href="{{ route('vendor-bills.create') }}?vendor_id={{ $vendor->id }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                + New Purchase Bill &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Bill Number</th>
                        <th class="py-3 px-4">Supplier Ref #</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Paid</th>
                        <th class="py-3 px-4">Balance Due</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($vendor->bills as $bill)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('vendor-bills.show', $bill) }}" class="hover:text-amber-700">
                                    {{ $bill->bill_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $bill->vendor_reference_no ?? '—' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $bill->bill_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->grand_total, 2) }}</td>
                            <td class="py-3 px-4 text-emerald-700 font-medium">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->paid_amount, 2) }}</td>
                            <td class="py-3 px-4 font-bold {{ $bill->due_amount > 0 ? 'text-amber-800' : 'text-slate-400' }}">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->due_amount, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                @if ($bill->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Paid</span>
                                @elseif ($bill->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">Partial</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('vendor-bills.show', $bill) }}" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-medium text-xs">View</a>
                                <a href="{{ route('vendor-bills.print', $bill) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs">Print</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">No purchase bills logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Made to Vendor Ledger -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Payments Made to Vendor</h3>
            <p class="text-xs text-slate-500">Record of outgoing payments made to {{ $vendor->name }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Payment Date</th>
                        <th class="py-3 px-4">Bill Applied</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4">Reference / Cheque #</th>
                        <th class="py-3 px-4">Amount Paid</th>
                        <th class="py-3 px-4">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($vendor->payments as $payment)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">
                                @if ($payment->bill)
                                    <a href="{{ route('vendor-bills.show', $payment->bill) }}" class="text-amber-800 hover:underline font-mono">
                                        {{ $payment->bill->bill_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">General Account</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $payment->reference_number ?? '—' }}</td>
                            <td class="py-3 px-4 font-bold text-amber-800">
                                -{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">{{ $payment->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No outgoing payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Record Vendor Payment Modal -->
<div id="vendorPaymentModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Make Payment to Vendor</h3>
                <p class="text-xs text-slate-500">To: {{ $vendor->name }} (Payable: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendor->current_balance, 2) }})</p>
            </div>
            <button onclick="document.getElementById('vendorPaymentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('vendors.payments.store', $vendor) }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label for="pay_amount" class="block font-semibold text-slate-700 mb-1">Amount Paid *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                    <input type="number" step="0.01" min="0.01" id="pay_amount" name="amount" value="{{ old('amount', max(0, $vendor->current_balance)) }}" required
                        class="w-full pl-7 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs font-bold text-amber-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="payment_date" class="block font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required
                        class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label for="payment_method" class="block font-semibold text-slate-700 mb-1">Payment Mode *</label>
                    <select id="payment_method" name="payment_method" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="cash">Cash Out</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cheque">Bank Cheque</option>
                        <option value="mobile">Mobile Banking</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="vendor_bill_id" class="block font-semibold text-slate-700 mb-1">Apply to Specific Purchase Bill (Optional)</label>
                <select id="vendor_bill_id" name="vendor_bill_id" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs">
                    <option value="">Auto-apply to oldest unpaid bills</option>
                    @foreach ($vendor->bills->where('payment_status', '!=', 'paid') as $uBill)
                        <option value="{{ $uBill->id }}">
                            {{ $uBill->bill_number }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($uBill->due_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ref_num" class="block font-semibold text-slate-700 mb-1">Reference / Cheque / Trx #</label>
                <input type="text" id="ref_num" name="reference_number" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs" placeholder="e.g. CHQ-99120">
            </div>

            <div>
                <label for="pay_notes" class="block font-semibold text-slate-700 mb-1">Notes</label>
                <input type="text" id="pay_notes" name="notes" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-xs" placeholder="Remarks...">
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('vendorPaymentModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-md shadow-amber-600/30">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

