<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $customerBill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-box {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 4mm !important;
            }
        }
        body {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body class="bg-slate-100 p-4 min-h-screen flex flex-col items-center justify-start text-slate-900 text-xs">
    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-[340px] w-full mb-3 flex items-center justify-between no-print">
        <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-sans text-xs font-semibold hover:bg-slate-700">
            &larr; Back to POS
        </a>
        <button onclick="window.print()" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white font-sans text-xs font-bold hover:bg-emerald-700 flex items-center gap-1 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print Receipt</span>
        </button>
    </div>

    <!-- 80mm Thermal Receipt Layout -->
    <div class="receipt-box max-w-[340px] w-full bg-white p-5 rounded-xl shadow-lg border border-slate-200 space-y-3">
        <!-- Shop Header -->
        <div class="text-center space-y-0.5">
            <div class="text-base font-black tracking-tight uppercase">{{ $settings['shop_name'] ?? 'THREE DOT MART' }}</div>
            <div class="text-[11px] text-slate-600 font-sans">{{ $settings['shop_tagline'] ?? 'Shop Accounting & Retail Mart' }}</div>
            @if (!empty($settings['shop_address']))
                <div class="text-[10px] text-slate-600 mt-0.5">{{ $settings['shop_address'] }}</div>
            @endif
            @if (!empty($settings['shop_phone']))
                <div class="text-[10px] text-slate-600">Tel: {{ $settings['shop_phone'] }}</div>
            @endif
        </div>

        <div class="border-t border-dashed border-slate-400"></div>

        <!-- Receipt Metadata -->
        <div class="text-[11px] space-y-0.5">
            <div class="flex justify-between">
                <span>RCVD: {{ $customerBill->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-bold">POS #1</span>
            </div>
            <div class="flex justify-between">
                <span>INVOICE:</span>
                <span class="font-bold font-mono">{{ $customerBill->bill_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>CASHIER:</span>
                <span>{{ $customerBill->user->name ?? 'Admin Desk' }}</span>
            </div>
            <div class="flex justify-between">
                <span>CUSTOMER:</span>
                <span class="font-bold">{{ $customerBill->customer ? $customerBill->customer->name : ($customerBill->customer_name ?? 'Walk-in') }}</span>
            </div>
        </div>

        <div class="border-t border-dashed border-slate-400"></div>

        <!-- Items Table -->
        <table class="w-full text-[11px] text-left">
            <thead>
                <tr class="border-b border-dashed border-slate-300 font-bold">
                    <th class="py-1">ITEM</th>
                    <th class="py-1 text-center">QTY</th>
                    <th class="py-1 text-right">PRICE</th>
                    <th class="py-1 text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dotted divide-slate-200">
                @foreach ($customerBill->items as $item)
                    <tr>
                        <td class="py-1 font-semibold pr-1">
                            {{ $item->item_name }}
                        </td>
                        <td class="py-1 text-center align-top">{{ $item->quantity }}</td>
                        <td class="py-1 text-right align-top">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-1 text-right font-bold align-top">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-dashed border-slate-400"></div>

        <!-- Financial Summary -->
        <div class="text-[11px] space-y-1">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->subtotal, 2) }}</span>
            </div>
            @if ($customerBill->discount > 0)
                <div class="flex justify-between text-slate-700">
                    <span>Discount:</span>
                    <span>-{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->discount, 2) }}</span>
                </div>
            @endif
            @if ($customerBill->tax > 0)
                <div class="flex justify-between">
                    <span>Tax / VAT:</span>
                    <span>+{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->tax, 2) }}</span>
                </div>
            @endif

            <div class="flex justify-between text-sm font-black pt-1 border-t border-dashed border-slate-400">
                <span>TOTAL PAYABLE:</span>
                <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->grand_total, 2) }}</span>
            </div>

            <div class="flex justify-between pt-1">
                <span>Payment ({{ strtoupper(str_replace('_', ' ', $customerBill->payment_method)) }}):</span>
                <span class="font-bold">{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->paid_amount, 2) }}</span>
            </div>

            @if ($customerBill->due_amount > 0)
                <div class="flex justify-between font-bold text-rose-700">
                    <span>BALANCE DUE:</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}{{ number_format($customerBill->due_amount, 2) }}</span>
                </div>
            @else
                <div class="flex justify-between text-slate-700">
                    <span>CHANGE:</span>
                    <span>{{ $settings['currency_symbol'] ?? '$' }}0.00</span>
                </div>
            @endif
        </div>

        <div class="border-t border-dashed border-slate-400"></div>

        <!-- Footer Notice & Barcode -->
        <div class="text-center text-[10px] text-slate-600 space-y-2 pt-1 font-sans">
            <p>{{ $settings['invoice_terms'] ?? 'Thank you for shopping with us! Please come again.' }}</p>

            <!-- Barcode Pattern Representation -->
            <div class="py-1">
                <div class="inline-block tracking-widest font-mono text-base font-bold">
                    ||||| | |||| || ||| |||| | |||
                </div>
                <div class="text-[9px] font-mono text-slate-400">{{ $customerBill->bill_number }}</div>
            </div>

            <div class="text-[9px] text-slate-400">
                Powered by Three Dot Accounting POS
            </div>
        </div>
    </div>

    <!-- Auto Print Trigger -->
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>

