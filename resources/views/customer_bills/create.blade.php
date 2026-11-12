@extends('layouts.app')

@section('title', 'New Sale Bill')
@section('page_heading', 'Create Customer Sale Bill')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <form id="saleBillForm" method="POST" action="{{ route('customer-bills.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Customer & Line Items Table -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer & Bill Info Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">1. Invoice & Customer Details</h2>
                        <a href="{{ route('customer-bills.index') }}" class="text-xs text-slate-500 hover:text-slate-800">&larr; Back to Invoices</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Invoice Number *</label>
                            <input type="text" name="bill_number" value="{{ old('bill_number', $nextBillNumber) }}" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold text-slate-800 bg-slate-50 text-xs">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Bill Date *</label>
                            <input type="date" name="bill_date" value="{{ old('bill_date', $today) }}" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-1">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-semibold text-slate-700">Select Customer</label>
                                <a href="{{ route('customers.create') }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline">+ New Customer</a>
                            </div>
                            <select id="customer_id" name="customer_id" onchange="toggleWalkinCustomer(this)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <option value="">-- Walk-in / Cash Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ (old('customer_id', request('customer_id')) == $c->id) ? 'selected' : '' }} data-balance="{{ $c->current_balance }}">
                                        {{ $c->name }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($c->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="walkin_name_div">
                            <label class="block font-semibold text-slate-700 mb-1">Walk-in Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', 'Walk-in Customer') }}"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs" placeholder="e.g. Counter Cash Customer">
                        </div>
                    </div>
                </div>

                <!-- Products & Line Items Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">2. Bill Items</h2>
                            <p class="text-xs text-slate-400">Inventory will be reduced automatically upon saving</p>
                        </div>
                        <button type="button" onclick="addRow()" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs border border-indigo-200 flex items-center gap-1 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Add Item Row</span>
                        </button>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left" id="itemsTable">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th class="py-2.5 px-3 min-w-[200px]">Item</th>
                                    <th class="py-2.5 px-3 w-28">Quantity</th>
                                    <th class="py-2.5 px-3 w-32">Unit Price</th>
                                    <th class="py-2.5 px-3 w-28 text-right">Total</th>
                                    <th class="py-2.5 px-2 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" class="divide-y divide-slate-100">
                                <!-- Initial Item Row -->
                                <tr class="item-row">
                                    <td class="py-2.5 px-3">
                                        <select name="items[0][item_id]" onchange="onItemChange(this, 0)" required
                                            class="item-select w-full p-2 rounded-xl border border-slate-300 text-xs">
                                            <option value="">-- Choose Product --</option>
                                            @foreach ($items as $itm)
                                                <option value="{{ $itm->id }}" data-price="{{ $itm->selling_price }}" data-stock="{{ $itm->stock_quantity }}" data-unit="{{ $itm->unit }}">
                                                    {{ $itm->name }} (Stock: {{ $itm->stock_quantity }} {{ $itm->unit }} | {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($itm->selling_price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="stock-info text-[10px] text-slate-400 mt-1"></div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" step="0.01" min="0.01" name="items[0][quantity]" value="1" oninput="calculateTotals()" required
                                            class="item-qty w-full p-2 rounded-xl border border-slate-300 text-xs text-right font-medium">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-2 text-slate-400 text-xs">{{ $settings['currency_symbol'] ?? '$' }}</span>
                                            <input type="number" step="0.01" min="0" name="items[0][unit_price]" value="0.00" oninput="calculateTotals()" required
                                                class="item-price w-full pl-6 pr-2 py-2 rounded-xl border border-slate-300 text-xs text-right font-semibold">
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 item-total">
                                        {{ $settings['currency_symbol'] ?? '$' }}0.00
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <button type="button" onclick="removeRow(this)" class="p-1 text-slate-300 hover:text-rose-500 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 text-xs mb-1">Invoice Notes / Customer Remarks</label>
                        <textarea name="notes" rows="2" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs" placeholder="e.g. Delivered to shop counter, thank you!"></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Col: Summary, Payment & Checkout -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 sticky top-20">
                    <h3 class="font-bold text-slate-900 text-sm pb-3 border-b border-slate-100">3. Checkout & Payment</h3>

                    <div class="space-y-3 text-xs">
                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay" class="font-bold text-slate-900 text-sm">{{ $settings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>

                        <!-- Discount -->
                        <div>
                            <div class="flex items-center justify-between mb-1 text-slate-700">
                                <label for="discount">Discount (-) </label>
                                <span class="text-[10px] text-slate-400">Flat Amount</span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                                <input type="number" step="0.01" min="0" id="discount" name="discount" value="0.00" oninput="calculateTotals()"
                                    class="w-full pl-7 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs text-right font-medium">
                            </div>
                        </div>

                        <!-- Tax / VAT -->
                        <div>
                            <div class="flex items-center justify-between mb-1 text-slate-700">
                                <label for="tax">Tax / VAT (+) </label>
                                <span class="text-[10px] text-slate-400">Flat Amount</span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                                <input type="number" step="0.01" min="0" id="tax" name="tax" value="0.00" oninput="calculateTotals()"
                                    class="w-full pl-7 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs text-right font-medium">
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                            <span class="font-bold text-slate-800 text-sm">Grand Total</span>
                            <span id="grandTotalDisplay" class="font-black text-indigo-700 text-xl">{{ $settings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>

                        <!-- Amount Paid Now -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-1">
                                <label for="paid_amount" class="font-semibold text-slate-800">Amount Paid Now</label>
                                <button type="button" onclick="payInFull()" class="text-[11px] text-indigo-600 hover:underline font-semibold">Pay Full</button>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                                <input type="number" step="0.01" min="0" id="paid_amount" name="paid_amount" value="0.00" oninput="calculateTotals()"
                                    class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-300 text-xs text-right font-bold text-emerald-800 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Due Balance -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <span class="font-semibold text-slate-700">Balance Due</span>
                            <span id="dueAmountDisplay" class="font-black text-rose-600 text-base">{{ $settings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs">
                                <option value="cash">Cash Payment</option>
                                <option value="card">Card / POS</option>
                                <option value="mobile_banking">Mobile Banking (bKash/Nagad/UPI)</option>
                                <option value="bank_transfer">Bank Wire</option>
                                <option value="credit">Credit (On Account)</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Save & Print Invoice</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const currency = '{{ $settings['currency_symbol'] ?? '$' }}';
    let rowIndex = 1;

    // Available items for row creation
    const availableItems = [
        @foreach ($items as $itm)
            { id: {{ $itm->id }}, name: "{{ addslashes($itm->name) }}", price: {{ $itm->selling_price }}, stock: {{ $itm->stock_quantity }}, unit: "{{ $itm->unit }}" },
        @endforeach
    ];

    function toggleWalkinCustomer(select) {
        const walkinDiv = document.getElementById('walkin_name_div');
        const custNameInput = document.getElementById('customer_name');
        if (select.value === '') {
            walkinDiv.classList.remove('hidden');
            custNameInput.value = 'Walk-in Customer';
        } else {
            walkinDiv.classList.add('hidden');
            const selectedText = select.options[select.selectedIndex].text.split(' (Due:')[0];
            custNameInput.value = selectedText;
        }
    }

    function addRow() {
        const tbody = document.getElementById('itemsTableBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';

        let optionsHtml = '<option value="">-- Choose Product --</option>';
        availableItems.forEach(item => {
            optionsHtml += `<option value="${item.id}" data-price="${item.price}" data-stock="${item.stock}" data-unit="${item.unit}">${item.name} (Stock: ${item.stock} ${item.unit} | ${currency}${item.price.toFixed(2)})</option>`;
        });

        tr.innerHTML = `
            <td class="py-2.5 px-3">
                <select name="items[${rowIndex}][item_id]" onchange="onItemChange(this, ${rowIndex})" required
                    class="item-select w-full p-2 rounded-xl border border-slate-300 text-xs">
                    ${optionsHtml}
                </select>
                <div class="stock-info text-[10px] text-slate-400 mt-1"></div>
            </td>
            <td class="py-2.5 px-3">
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" value="1" oninput="calculateTotals()" required
                    class="item-qty w-full p-2 rounded-xl border border-slate-300 text-xs text-right font-medium">
            </td>
            <td class="py-2.5 px-3">
                <div class="relative">
                    <span class="absolute left-2.5 top-2 text-slate-400 text-xs">${currency}</span>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" value="0.00" oninput="calculateTotals()" required
                        class="item-price w-full pl-6 pr-2 py-2 rounded-xl border border-slate-300 text-xs text-right font-semibold">
                </div>
            </td>
            <td class="py-2.5 px-3 text-right font-bold text-slate-900 item-total">
                ${currency}0.00
            </td>
            <td class="py-2.5 px-2 text-center">
                <button type="button" onclick="removeRow(this)" class="p-1 text-slate-300 hover:text-rose-500 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotals();
        } else {
            alert('A bill must contain at least 1 item.');
        }
    }

    function onItemChange(select, idx) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const priceInput = row.querySelector('.item-price');
        const qtyInput = row.querySelector('.item-qty');
        const stockInfo = row.querySelector('.stock-info');

        if (selectedOption && selectedOption.value) {
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';

            priceInput.value = price.toFixed(2);
            stockInfo.innerHTML = `Available Stock: <strong class="${stock <= 0 ? 'text-rose-600' : 'text-slate-700'}">${stock} ${unit}</strong>`;
        } else {
            priceInput.value = '0.00';
            stockInfo.innerHTML = '';
        }

        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('.item-row');

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const lineTotal = qty * price;
            subtotal += lineTotal;

            row.querySelector('.item-total').textContent = currency + lineTotal.toFixed(2);
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount + tax);
        const paidAmountInput = document.getElementById('paid_amount');
        let paidAmount = parseFloat(paidAmountInput.value) || 0;

        const dueAmount = Math.max(0, grandTotal - paidAmount);

        document.getElementById('subtotalDisplay').textContent = currency + subtotal.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = currency + grandTotal.toFixed(2);
        document.getElementById('dueAmountDisplay').textContent = currency + dueAmount.toFixed(2);
    }

    function payInFull() {
        let subtotal = 0;
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            subtotal += (qty * price);
        });
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount + tax);

        document.getElementById('paid_amount').value = grandTotal.toFixed(2);
        calculateTotals();
    }

    document.addEventListener('DOMContentLoaded', function () {
        calculateTotals();
    });
</script>
@endpush
@endsection

