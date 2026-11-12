<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $customerBill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800 antialiased font-sans">
    <div class="max-w-3xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('customer-bills.show', $customerBill) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to Bill Details
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs shadow-md hover:bg-indigo-700 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print Invoice</span>
        </button>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="print-container max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-xl border border-slate-200 space-y-6">
        <!-- Shop Header & Invoice Meta -->
        <div class="flex items-start justify-between pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black flex items-center justify-center text-sm">3•</span>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">{{ $settings['shop_name'] ?? 'Three Dot Mart' }}</h1>
                </div>
                <p class="text-xs text-slate-500">{{ $settings['shop_tagline'] ?? 'Shop Accounting & Inventory' }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $settings['shop_address'] ?? '' }}</p>
                <p class="text-xs text-slate-500">Phone: {{ $settings['shop_phone'] ?? '' }} | Email: {{ $settings['shop_email'] ?? '' }}</p>
            </div>

            <div class="text-right">
                <div class="text-2xl font-black text-indigo-700 font-mono tracking-tight uppercase">INVOICE</div>
                <div class="font-mono font-bold text-slate-800 text-sm mt-1">{{ $customerBill->bill_number }}</div>
                <div class="text-xs text-slate-500 mt-1">Date: {{ $customerBill->bill_date->format('M d, Y') }}</div>
                @if ($customerBill->due_date)
                    <div class="text-xs text-slate-500">Due Date: {{ $customerBill->due_date->format('M d, Y') }}</div>
                @endif
                <div class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    {{ $customerBill->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($customerBill->payment_status === 'partial' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                    Status: {{ $customerBill->payment_status }}
                </div>
            </div>
        </div>

        <!-- Customer Bill-To Info -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Customer / Bill To:</span>
                <div class="font-bold text-slate-900 text-sm">
                    {{ $customerBill->customer ? $customerBill->customer->name : ($customerBill->customer_name ?? 'Walk-in Customer') }}
                </div>
                @if ($customerBill->customer)
                    @if ($customerBill->customer->phone)
                        <div class="text-slate-600 mt-0.5">Phone: {{ $customerBill->customer->phone }}</div>
                    @endif
                    @if ($customerBill->customer->email)
                        <div class="text-slate-500">{{ $customerBill->customer->email }}</div>
                    @endif
                    @if ($customerBill->customer->address)
                        <div class="text-slate-500">{{ $customerBill->customer->address }}</div>
                    @endif
                @endif
            </div>

            <div class="text-right">
                <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Payment Method:</span>
                <div class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $customerBill->payment_method) }}</div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px] border-y border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">Item Description</th>
                        <th class="py-2.5 px-3 text-right">Price</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($customerBill->items as $idx => $item)
                        <tr>
                            <td class="py-2.5 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $item->item_name }}</td>
                            <td class="py-2.5 px-3 text-right text-slate-600">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-medium">{{ $item->quantity }}</td>
                            <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Due -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <div class="w-64 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-800">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->subtotal, 2) }}</span>
                </div>
                @if ($customerBill->discount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount:</span>
                        <span>-{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->discount, 2) }}</span>
                    </div>
                @endif
                @if ($customerBill->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax / VAT:</span>
                        <span>+{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->tax, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-sm text-slate-900 pt-1.5 border-t border-slate-200">
                    <span>Grand Total:</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-medium">
                    <span>Amount Paid:</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-rose-600 pt-1 border-t border-slate-100">
                    <span>Balance Due:</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Terms & Signature Footer -->
        <div class="pt-8 border-t border-slate-200 text-xs text-slate-500 space-y-6">
            @if ($customerBill->notes)
                <p class="italic">Note: {{ $customerBill->notes }}</p>
            @endif

            <p class="text-[11px] text-slate-400 text-center">
                {{ $settings['invoice_terms'] ?? 'Thank you for your business!' }}
            </p>

            <div class="flex justify-between items-end pt-12 text-[11px] text-slate-400">
                <div class="border-t border-slate-300 pt-1 w-40 text-center">Customer Signature</div>
                <div class="border-t border-slate-300 pt-1 w-40 text-center">Authorized Store Sign</div>
            </div>
        </div>
    </div>
</body>
</html>

