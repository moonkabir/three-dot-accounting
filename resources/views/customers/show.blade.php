@extends('layouts.app')

@section('title', 'Ledger - ' . $customer->name)
@section('page_heading', 'Customer Ledger & Account Statement')

@section('content')
<div class="space-y-6">
    <!-- Top Customer Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-black text-xl flex items-center justify-center shadow-md">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $customer->name }}</h2>
                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                        @if ($customer->phone)
                            <span>📞 {{ $customer->phone }}</span>
                        @endif
                        @if ($customer->email)
                            <span>✉️ {{ $customer->email }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($customer->address)
                <p class="text-xs text-slate-500 mt-2 pl-1">📍 {{ $customer->address }}</p>
            @endif
        </div>

        <!-- Balance Metrics & Action Buttons -->
        <div class="flex flex-wrap items-center gap-4">
            <div class="text-right px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Outstanding Balance</div>
                <div class="text-2xl font-black {{ $customer->current_balance > 0 ? 'text-rose-600' : ($customer->current_balance < 0 ? 'text-blue-600' : 'text-emerald-700') }}">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customer->current_balance, 2) }}
                </div>
                <div class="text-[10px] text-slate-400">
                    {{ $customer->current_balance > 0 ? 'Due from Customer' : ($customer->current_balance < 0 ? 'Advance Balance' : 'Account Settled') }}
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <button type="button" onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Receive Payment</span>
                </button>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('customer-bills.create') }}?customer_id={{ $customer->id }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-medium text-xs border border-indigo-200 text-center flex-1">
                        + New Bill
                    </a>
                    <a href="{{ route('customers.edit', $customer) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium text-xs text-center">
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
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customer->opening_balance, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Sales Invoiced</span>
            <div class="text-lg font-bold text-indigo-700 mt-0.5">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalBilled, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Paid / Cleared</span>
            <div class="text-lg font-bold text-emerald-700 mt-0.5">
                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPaid, 2) }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Bills Count</span>
            <div class="text-lg font-bold text-slate-800 mt-0.5">
                {{ $customer->bills->count() }} bills
            </div>
        </div>
    </div>

    <!-- Customer Invoices & Bills Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Customer Invoices & Bills</h3>
                <p class="text-xs text-slate-500">Sales records generated for {{ $customer->name }}</p>
            </div>
            <a href="{{ route('customer-bills.create') }}?customer_id={{ $customer->id }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                + Create Bill &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Bill Number</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Grand Total</th>
                        <th class="py-3 px-4">Paid</th>
                        <th class="py-3 px-4">Balance Due</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customer->bills as $bill)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('customer-bills.show', $bill) }}" class="hover:text-indigo-600">
                                    {{ $bill->bill_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $bill->bill_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->grand_total, 2) }}</td>
                            <td class="py-3 px-4 text-emerald-700 font-medium">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($bill->paid_amount, 2) }}</td>
                            <td class="py-3 px-4 font-bold {{ $bill->due_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
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
                                <a href="{{ route('customer-bills.show', $bill) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-xs">View</a>
                                <a href="{{ route('customer-bills.print', $bill) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs">Print</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No invoices recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Received History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Payments Received Ledger</h3>
            <p class="text-xs text-slate-500">History of money received from customer</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Payment Date</th>
                        <th class="py-3 px-4">Applied to Bill</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4">Ref Number</th>
                        <th class="py-3 px-4">Amount Paid</th>
                        <th class="py-3 px-4">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customer->payments as $payment)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">
                                @if ($payment->bill)
                                    <a href="{{ route('customer-bills.show', $payment->bill) }}" class="text-indigo-600 hover:underline font-mono">
                                        {{ $payment->bill->bill_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">General Account</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $payment->reference_number ?? '—' }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                +{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">{{ $payment->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No payment receipts logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Receive Payment</h3>
                <p class="text-xs text-slate-500">From {{ $customer->name }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customer->current_balance, 2) }})</p>
            </div>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('customers.payments.store', $customer) }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label for="pay_amount" class="block font-semibold text-slate-700 mb-1">Payment Amount Received *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                    <input type="number" step="0.01" min="0.01" id="pay_amount" name="amount" value="{{ old('amount', max(0, $customer->current_balance)) }}" required
                        class="w-full pl-7 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs font-bold text-emerald-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="payment_date" class="block font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required
                        class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label for="payment_method" class="block font-semibold text-slate-700 mb-1">Payment Mode *</label>
                    <select id="payment_method" name="payment_method" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs">
                        <option value="cash">Cash Counter</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="card">Card / POS</option>
                        <option value="mobile">Mobile Banking</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="customer_bill_id" class="block font-semibold text-slate-700 mb-1">Apply to Specific Invoice (Optional)</label>
                <select id="customer_bill_id" name="customer_bill_id" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs">
                    <option value="">Auto-apply to oldest unpaid invoices</option>
                    @foreach ($customer->bills->where('payment_status', '!=', 'paid') as $uBill)
                        <option value="{{ $uBill->id }}">
                            {{ $uBill->bill_number }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($uBill->due_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ref_num" class="block font-semibold text-slate-700 mb-1">Transaction Ref # (Cheque / TrxID / Slip)</label>
                <input type="text" id="ref_num" name="reference_number" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs" placeholder="e.g. TXN-9812">
            </div>

            <div>
                <label for="pay_notes" class="block font-semibold text-slate-700 mb-1">Notes</label>
                <input type="text" id="pay_notes" name="notes" class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-xs" placeholder="Payment receipt remark...">
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-md shadow-emerald-600/30">
                    Record Receipt
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

