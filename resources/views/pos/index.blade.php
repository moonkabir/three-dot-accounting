<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal - {{ $settings['shop_name'] ?? 'Three Dot Accounting' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar for POS terminal */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .product-card:active { transform: scale(0.97); }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans h-screen flex flex-col overflow-hidden select-none">
    <!-- Top POS Navbar -->
    <header class="bg-slate-900 text-white px-5 py-3 flex items-center justify-between border-b border-slate-800 shadow-md flex-shrink-0 z-20">
        <!-- Brand & Terminal Info -->
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity" title="Back to Dashboard">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center font-black text-white text-base shadow">
                    3•
                </div>
                <div>
                    <div class="font-bold text-sm tracking-tight leading-none text-white">{{ $settings['shop_name'] ?? 'Three Dot Mart' }}</div>
                    <div class="text-[11px] text-emerald-400 font-semibold tracking-wider uppercase flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        POS Terminal #01
                    </div>
                </div>
            </a>
        </div>

        <!-- Scanner & Search Bar -->
        <div class="flex-1 max-w-xl mx-6 hidden md:block">
            <div class="relative">
                <input type="text" id="barcodeScannerInput" placeholder="Scan Barcode / SKU or type product name... (Press Enter to Add)"
                    class="w-full bg-slate-800 text-white placeholder-slate-400 pl-10 pr-4 py-2 rounded-xl border border-slate-700 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none focus:bg-slate-850">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            </div>
        </div>

        <!-- Right Quick Actions -->
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <div class="text-xs font-bold text-slate-200" id="liveClock">--:--:--</div>
                <div class="text-[11px] text-slate-400">{{ date('M d, Y') }}</div>
            </div>

            <div class="h-6 w-px bg-slate-800 hidden sm:block"></div>

            <div class="flex items-center gap-2">
                <a href="{{ route('customer-bills.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium transition-colors">
                    Bills List
                </a>
                <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main POS Screen Layout (2 Columns: Left = Product Catalog, Right = Cart & Fast Checkout) -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        <!-- LEFT COLUMN: Product Catalog & Search Filter -->
        <section class="flex-1 flex flex-col bg-slate-50 border-r border-slate-200 overflow-hidden">
            <!-- Search & Category Filters Bar -->
            <div class="p-4 bg-white border-b border-slate-200 space-y-3 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Mobile Search Input -->
                    <div class="relative flex-1 md:hidden">
                        <input type="text" id="mobileSearchInput" placeholder="Search product or SKU..."
                            class="w-full bg-slate-50 pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Category Pills Horizontal Scroll -->
                    <div class="flex-1 overflow-x-auto flex items-center gap-2 pb-1 scrollbar-thin">
                        <button type="button" onclick="filterCategory('all')" id="catBtn-all"
                            class="category-pill active px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap bg-indigo-600 text-white shadow-sm transition-all">
                            All Products ({{ $items->count() }})
                        </button>
                        @foreach ($categories as $cat)
                            <button type="button" onclick="filterCategory('{{ $cat->id }}')" id="catBtn-{{ $cat->id }}"
                                class="category-pill px-3.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">
                                {{ $cat->name }} ({{ $cat->items_count }})
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 p-4 overflow-y-auto">
                <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3.5">
                    @forelse ($items as $item)
                        <div class="product-card bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all cursor-pointer flex flex-col justify-between relative group"
                             data-id="{{ $item->id }}"
                             data-sku="{{ strtolower($item->sku) }}"
                             data-name="{{ strtolower($item->name) }}"
                             data-category="{{ $item->category_id }}"
                             data-price="{{ $item->selling_price }}"
                             data-stock="{{ $item->stock_quantity }}"
                             data-unit="{{ $item->unit }}"
                             onclick="addToCart({{ $item->id }})">

                            <!-- Stock Indicator Badge -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium truncate max-w-[100px]">
                                    {{ $item->sku }}
                                </span>
                                @if ($item->stock_quantity <= 0)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-100 text-rose-700">Out of Stock</span>
                                @elseif ($item->is_low_stock)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800" title="Low stock threshold">
                                        {{ $item->stock_quantity }} left
                                    </span>
                                @else
                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700">
                                        {{ $item->stock_quantity }} {{ $item->unit }}
                                    </span>
                                @endif
                            </div>

                            <!-- Product Name & Category -->
                            <div class="my-1">
                                <div class="font-bold text-xs text-slate-800 line-clamp-2 leading-tight group-hover:text-indigo-600 transition-colors">
                                    {{ $item->name }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate mt-0.5">
                                    {{ $item->category->name ?? 'General' }}
                                </div>
                            </div>

                            <!-- Price & Add Action -->
                            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <div class="text-sm font-black text-slate-900 font-mono">
                                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($item->selling_price, 2) }}
                                </div>
                                <span class="w-7 h-7 rounded-xl bg-indigo-50 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-slate-400">
                            No active products in inventory. <a href="{{ route('inventory.create') }}" class="text-indigo-600 font-bold hover:underline">Add items</a> to begin selling.
                        </div>
                    @endforelse
                </div>

                <!-- No Results State -->
                <div id="noResults" class="hidden py-16 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="text-xs font-medium">No products match your search or filter.</p>
                </div>
            </div>
        </section>

        <!-- RIGHT COLUMN: Live Cart, Customer Selector & Checkout -->
        <section class="w-full lg:w-[440px] xl:w-[480px] bg-white flex flex-col h-full border-t lg:border-t-0 shadow-lg flex-shrink-0 z-10">
            <!-- Customer Picker Header -->
            <div class="p-4 border-b border-slate-200 bg-slate-50/70 flex items-center gap-2 flex-shrink-0">
                <div class="relative flex-1">
                    <select id="posCustomerId" onchange="onCustomerChange(this)" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="" data-name="Walk-in Customer" data-balance="0">
                            👤 Walk-in / Cash Customer
                        </option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-balance="{{ $c->current_balance }}">
                                👤 {{ $c->name }} (Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($c->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" onclick="document.getElementById('posNewCustomerModal').classList.remove('hidden')" class="p-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-medium transition-colors" title="Quick Add Customer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </button>

                <button type="button" onclick="clearCart()" class="p-2 rounded-xl bg-white border border-slate-300 hover:bg-rose-50 hover:text-rose-600 text-slate-400 text-xs font-medium transition-colors" title="Clear Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>

            <!-- Cart Items Container (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2.5" id="cartContainer">
                <!-- Empty Cart State -->
                <div id="emptyCartView" class="h-full flex flex-col items-center justify-center text-center py-16 text-slate-400">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="font-bold text-xs text-slate-600">Cart is Empty</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Click any product or scan SKU to add items</div>
                </div>

                <!-- Items will be injected dynamically by JavaScript -->
                <div id="cartItemsList" class="space-y-2"></div>
            </div>

            <!-- Cart Calculations & Tender Checkout (Pinned Bottom) -->
            <div class="p-4 bg-slate-50/90 border-t border-slate-200 space-y-3 flex-shrink-0">
                <!-- Subtotal, Discount, Tax -->
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal (<span id="cartCount">0</span> items / <span id="cartUnits">0</span> units)</span>
                        <span id="posSubtotal" class="font-bold text-slate-900">{{ $settings['currency_symbol'] ?? '$' }}0.00</span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="flex items-center gap-1 text-slate-600">
                            Discount (-)
                        </span>
                        <div class="relative w-28">
                            <span class="absolute left-2.5 top-1.5 text-slate-400 text-xs">{{ $settings['currency_symbol'] ?? '$' }}</span>
                            <input type="number" step="0.01" min="0" id="posDiscount" value="0.00" oninput="renderCartSummary()"
                                class="w-full pl-6 pr-2 py-1 rounded-lg border border-slate-300 text-xs text-right font-medium bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-600">Tax / VAT (+)</span>
                        <div class="relative w-28">
                            <span class="absolute left-2.5 top-1.5 text-slate-400 text-xs">{{ $settings['currency_symbol'] ?? '$' }}</span>
                            <input type="number" step="0.01" min="0" id="posTax" value="0.00" oninput="renderCartSummary()"
                                class="w-full pl-6 pr-2 py-1 rounded-lg border border-slate-300 text-xs text-right font-medium bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Total Amount Display Banner -->
                <div class="p-3 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between shadow-md">
                    <div>
                        <div class="text-[10px] font-semibold tracking-wider uppercase text-indigo-300">Total Payable</div>
                        <div class="text-[11px] text-slate-400">Net after discount & tax</div>
                    </div>
                    <div class="text-2xl font-black tracking-tight font-mono text-emerald-400" id="posGrandTotal">
                        {{ $settings['currency_symbol'] ?? '$' }}0.00
                    </div>
                </div>

                <!-- Payment Modes Selector -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Payment Method</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="setPaymentMethod('cash')" id="payBtn-cash"
                            class="pay-method-btn active py-1.5 rounded-xl border border-indigo-600 bg-indigo-50 text-indigo-700 font-bold text-[11px] text-center transition-all">
                            💵 Cash
                        </button>
                        <button type="button" onclick="setPaymentMethod('card')" id="payBtn-card"
                            class="pay-method-btn py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-medium text-[11px] text-center hover:bg-slate-100 transition-all">
                            💳 Card
                        </button>
                        <button type="button" onclick="setPaymentMethod('mobile_banking')" id="payBtn-mobile_banking"
                            class="pay-method-btn py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-medium text-[11px] text-center hover:bg-slate-100 transition-all">
                            📱 Mobile
                        </button>
                        <button type="button" onclick="setPaymentMethod('credit')" id="payBtn-credit"
                            class="pay-method-btn py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-medium text-[11px] text-center hover:bg-slate-100 transition-all">
                            📝 Credit
                        </button>
                    </div>
                </div>

                <!-- Quick Tender Buttons & Cash Input -->
                <div class="space-y-1.5">
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="tenderCash(10)" class="flex-1 py-1 px-1.5 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-800 text-[11px] font-bold transition-colors">
                            {{ $settings['currency_symbol'] ?? '$' }}10
                        </button>
                        <button type="button" onclick="tenderCash(20)" class="flex-1 py-1 px-1.5 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-800 text-[11px] font-bold transition-colors">
                            {{ $settings['currency_symbol'] ?? '$' }}20
                        </button>
                        <button type="button" onclick="tenderCash(50)" class="flex-1 py-1 px-1.5 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-800 text-[11px] font-bold transition-colors">
                            {{ $settings['currency_symbol'] ?? '$' }}50
                        </button>
                        <button type="button" onclick="tenderCash(100)" class="flex-1 py-1 px-1.5 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-800 text-[11px] font-bold transition-colors">
                            {{ $settings['currency_symbol'] ?? '$' }}100
                        </button>
                        <button type="button" onclick="tenderExact()" class="flex-1 py-1 px-1.5 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-800 text-[11px] font-black transition-colors">
                            Exact
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Amount Paid Received</label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-2 text-slate-400 text-xs">{{ $settings['currency_symbol'] ?? '$' }}</span>
                                <input type="number" step="0.01" min="0" id="posPaidAmount" value="0.00" oninput="renderChange()"
                                    class="w-full pl-6 pr-2 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5" id="changeOrDueLabel">Change to Return</label>
                            <div id="changeOrDueDisplay" class="py-1.5 px-3 rounded-xl bg-slate-100 border border-slate-200 text-xs font-black text-emerald-700">
                                {{ $settings['currency_symbol'] ?? '$' }}0.00
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Big Checkout Button -->
                <div>
                    <button type="button" id="btnCheckout" onclick="processCheckout()"
                        class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-sm shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>COMPLETE SALE & PRINT (F2)</span>
                    </button>
                </div>
            </div>
        </section>
    </div>

    <!-- Quick Add Customer Modal -->
    <div id="posNewCustomerModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Add New Customer</h3>
                <button onclick="document.getElementById('posNewCustomerModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>

            <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)" class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Customer Name *</label>
                    <input type="text" id="qcName" required class="w-full p-2.5 rounded-xl border border-slate-300 text-xs" placeholder="e.g. Sarah Jenkins">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="qcPhone" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs" placeholder="+1 (555) 123-4567">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email (Optional)</label>
                    <input type="email" id="qcEmail" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs" placeholder="sarah@example.com">
                </div>

                <div class="mt-5 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('posNewCustomerModal').classList.add('hidden')" class="px-3 py-2 rounded-xl text-slate-600 text-xs">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm">Save & Select</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sale Success & Receipt Print Modal -->
    <div id="saleSuccessModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl">
                ✓
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900">Sale Completed!</h3>
                <p class="text-xs text-slate-500 mt-1" id="successBillNumber">Invoice #INV-20261112-0001</p>
                <div class="text-2xl font-black text-slate-900 font-mono mt-2" id="successGrandTotal">$0.00</div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 text-xs text-slate-600 flex justify-between">
                <span>Change Returned:</span>
                <span class="font-bold text-emerald-700" id="successChangeAmount">$0.00</span>
            </div>

            <div class="space-y-2 pt-2">
                <a id="successPrintLink" href="#" target="_blank"
                   class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow flex items-center justify-center gap-1.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Print Thermal Receipt</span>
                </a>

                <button type="button" onclick="closeSuccessModal()"
                    class="w-full py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                    Next Sale (Esc)
                </button>
            </div>
        </div>
    </div>

    <!-- Product Database & POS Client State Script -->
    <script>
        const currency = '{{ $settings['currency_symbol'] ?? '$' }}';
        const csrfToken = '{{ csrf_token() }}';

        // Master Product Catalog in Memory
        const catalog = {
            @foreach ($items as $item)
                {{ $item->id }}: {
                    id: {{ $item->id }},
                    sku: '{{ addslashes($item->sku) }}',
                    name: '{{ addslashes($item->name) }}',
                    price: {{ $item->selling_price }},
                    cost: {{ $item->cost_price }},
                    stock: {{ $item->stock_quantity }},
                    unit: '{{ addslashes($item->unit) }}',
                    category_id: '{{ $item->category_id }}',
                },
            @endforeach
        };

        // Live POS Cart: array of { id, quantity, price }
        let cart = [];
        let activeCategory = 'all';
        let activePaymentMethod = 'cash';

        // Clock Update
        function updateClock() {
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString();
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Filter products by Category
        function filterCategory(catId) {
            activeCategory = catId;
            document.querySelectorAll('.category-pill').forEach(btn => {
                btn.classList.remove('active', 'bg-indigo-600', 'text-white', 'shadow-sm');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            });
            const activeBtn = document.getElementById('catBtn-' + catId);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-indigo-600', 'text-white', 'shadow-sm');
                activeBtn.classList.remove('bg-slate-100', 'text-slate-700');
            }
            applyProductFilters();
        }

        // Live Search & Barcode Scan Filter
        const searchInput = document.getElementById('barcodeScannerInput');
        const mobileSearch = document.getElementById('mobileSearchInput');

        if (searchInput) searchInput.addEventListener('input', applyProductFilters);
        if (mobileSearch) mobileSearch.addEventListener('input', () => {
            searchInput.value = mobileSearch.value;
            applyProductFilters();
        });

        // Barcode enter key trigger
        if (searchInput) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim().toLowerCase();
                    if (!query) return;

                    // Match exact SKU first
                    for (let id in catalog) {
                        if (catalog[id].sku.toLowerCase() === query) {
                            addToCart(catalog[id].id);
                            this.value = '';
                            applyProductFilters();
                            return;
                        }
                    }

                    // Otherwise match first visible item
                    const firstVisible = document.querySelector('.product-card:not(.hidden)');
                    if (firstVisible) {
                        const pid = parseInt(firstVisible.getAttribute('data-id'));
                        addToCart(pid);
                        this.value = '';
                        applyProductFilters();
                    }
                }
            });
        }

        function applyProductFilters() {
            const query = (searchInput.value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const sku = card.getAttribute('data-sku');
                const cat = card.getAttribute('data-category');

                const matchesCat = (activeCategory === 'all' || cat === activeCategory);
                const matchesQuery = (!query || name.includes(query) || sku.includes(query));

                if (matchesCat && matchesQuery) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noResults = document.getElementById('noResults');
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        // Add to Cart
        function addToCart(itemId) {
            const product = catalog[itemId];
            if (!product) return;

            const existing = cart.find(c => c.id === itemId);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: product.name,
                    sku: product.sku,
                    unit: product.unit,
                    price: product.price,
                    cost: product.cost,
                    quantity: 1,
                    stock: product.stock
                });
            }

            renderCart();
            tenderExact();
        }

        // Update Cart Item Quantity
        function updateCartQty(itemId, newQty) {
            const item = cart.find(c => c.id === itemId);
            if (!item) return;

            const qty = parseFloat(newQty);
            if (qty <= 0) {
                removeFromCart(itemId);
            } else {
                item.quantity = qty;
                renderCart();
            }
        }

        function incrementCartQty(itemId) {
            const item = cart.find(c => c.id === itemId);
            if (item) {
                item.quantity += 1;
                renderCart();
            }
        }

        function decrementCartQty(itemId) {
            const item = cart.find(c => c.id === itemId);
            if (item) {
                if (item.quantity > 1) {
                    item.quantity -= 1;
                    renderCart();
                } else {
                    removeFromCart(itemId);
                }
            }
        }

        function removeFromCart(itemId) {
            cart = cart.filter(c => c.id !== itemId);
            renderCart();
        }

        function clearCart() {
            if (cart.length > 0) {
                if (confirm('Clear all items from the current cart?')) {
                    cart = [];
                    renderCart();
                }
            }
        }

        // Render Cart UI
        function renderCart() {
            const cartList = document.getElementById('cartItemsList');
            const emptyView = document.getElementById('emptyCartView');

            if (cart.length === 0) {
                cartList.innerHTML = '';
                emptyView.classList.remove('hidden');
            } else {
                emptyView.classList.add('hidden');
                let html = '';

                cart.forEach(item => {
                    const lineTotal = item.quantity * item.price;
                    html += `
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/90 shadow-sm flex items-center justify-between gap-3 text-xs">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-800 truncate leading-tight">${item.name}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    ${currency}${item.price.toFixed(2)} &times; ${item.quantity} ${item.unit}
                                </div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                                <button type="button" onclick="decrementCartQty(${item.id})"
                                    class="w-6 h-6 rounded-md bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shadow-xs transition-colors">
                                    -
                                </button>
                                <input type="number" step="1" min="1" value="${item.quantity}"
                                    onchange="updateCartQty(${item.id}, this.value)"
                                    class="w-10 text-center font-bold text-xs bg-transparent border-none p-0 focus:outline-none">
                                <button type="button" onclick="incrementCartQty(${item.id})"
                                    class="w-6 h-6 rounded-md bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shadow-xs transition-colors">
                                    +
                                </button>
                            </div>

                            <!-- Line Total & Remove -->
                            <div class="text-right flex items-center gap-2">
                                <div class="font-bold font-mono text-slate-900">${currency}${lineTotal.toFixed(2)}</div>
                                <button type="button" onclick="removeFromCart(${item.id})" class="text-slate-300 hover:text-rose-500 p-0.5 rounded">
                                    &times;
                                </button>
                            </div>
                        </div>
                    `;
                });

                cartList.innerHTML = html;
            }

            renderCartSummary();
        }

        // Calculate Totals & Summary
        function getCartTotals() {
            let subtotal = 0;
            let units = 0;

            cart.forEach(c => {
                subtotal += (c.quantity * c.price);
                units += c.quantity;
            });

            const discount = parseFloat(document.getElementById('posDiscount').value) || 0;
            const tax = parseFloat(document.getElementById('posTax').value) || 0;
            const grandTotal = Math.max(0, subtotal - discount + tax);

            return {
                count: cart.length,
                units: units,
                subtotal: subtotal,
                discount: discount,
                tax: tax,
                grandTotal: grandTotal
            };
        }

        function renderCartSummary() {
            const totals = getCartTotals();

            document.getElementById('cartCount').textContent = totals.count;
            document.getElementById('cartUnits').textContent = totals.units;
            document.getElementById('posSubtotal').textContent = currency + totals.subtotal.toFixed(2);
            document.getElementById('posGrandTotal').textContent = currency + totals.grandTotal.toFixed(2);

            renderChange();
        }

        // Payment Mode
        function setPaymentMethod(method) {
            activePaymentMethod = method;
            document.querySelectorAll('.pay-method-btn').forEach(btn => {
                btn.classList.remove('active', 'border-indigo-600', 'bg-indigo-50', 'text-indigo-700', 'font-bold');
                btn.classList.add('border-slate-300', 'bg-white', 'text-slate-700', 'font-medium');
            });

            const activeBtn = document.getElementById('payBtn-' + method);
            if (activeBtn) {
                activeBtn.classList.add('active', 'border-indigo-600', 'bg-indigo-50', 'text-indigo-700', 'font-bold');
                activeBtn.classList.remove('border-slate-300', 'bg-white', 'text-slate-700', 'font-medium');
            }

            if (method === 'credit') {
                document.getElementById('posPaidAmount').value = '0.00';
            } else if (document.getElementById('posPaidAmount').value === '0.00' && cart.length > 0) {
                tenderExact();
            }
            renderChange();
        }

        // Tender helpers
        function tenderCash(amount) {
            document.getElementById('posPaidAmount').value = amount.toFixed(2);
            renderChange();
        }

        function tenderExact() {
            const totals = getCartTotals();
            document.getElementById('posPaidAmount').value = totals.grandTotal.toFixed(2);
            renderChange();
        }

        function renderChange() {
            const totals = getCartTotals();
            const paid = parseFloat(document.getElementById('posPaidAmount').value) || 0;

            const changeOrDueLabel = document.getElementById('changeOrDueLabel');
            const changeOrDueDisplay = document.getElementById('changeOrDueDisplay');

            if (paid >= totals.grandTotal) {
                const change = paid - totals.grandTotal;
                changeOrDueLabel.textContent = 'Change to Return';
                changeOrDueDisplay.className = 'py-1.5 px-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-black text-emerald-700';
                changeOrDueDisplay.textContent = currency + change.toFixed(2);
            } else {
                const due = totals.grandTotal - paid;
                changeOrDueLabel.textContent = 'Balance Due (Credit)';
                changeOrDueDisplay.className = 'py-1.5 px-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-black text-rose-600';
                changeOrDueDisplay.textContent = currency + due.toFixed(2);
            }
        }

        function onCustomerChange(select) {
            // Customer changed
            renderChange();
        }

        // Process Checkout
        async function processCheckout() {
            if (cart.length === 0) {
                alert('Please add at least 1 product to the cart before checkout.');
                return;
            }

            const totals = getCartTotals();
            const custSelect = document.getElementById('posCustomerId');
            const custId = custSelect.value || null;
            const custOption = custSelect.options[custSelect.selectedIndex];
            const custName = custOption ? custOption.getAttribute('data-name') : 'Walk-in Customer';

            const paidAmount = parseFloat(document.getElementById('posPaidAmount').value) || 0;

            const btn = document.getElementById('btnCheckout');
            btn.disabled = true;
            btn.innerHTML = `<span class="animate-spin inline-block mr-2">⚙</span> Processing...`;

            const payload = {
                customer_id: custId,
                customer_name: custName,
                discount: totals.discount,
                tax: totals.tax,
                paid_amount: paidAmount,
                payment_method: activePaymentMethod,
                notes: 'POS checkout',
                items: cart.map(c => ({
                    item_id: c.id,
                    quantity: c.quantity,
                    unit_price: c.price
                }))
            };

            try {
                const response = await fetch('{{ route('pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Update client-side stock numbers
                    cart.forEach(c => {
                        if (catalog[c.id]) {
                            catalog[c.id].stock = Math.max(0, catalog[c.id].stock - c.quantity);
                            const card = document.querySelector(`.product-card[data-id="${c.id}"]`);
                            if (card) {
                                card.setAttribute('data-stock', catalog[c.id].stock);
                            }
                        }
                    });

                    // Show success modal & thermal receipt trigger
                    document.getElementById('successBillNumber').textContent = result.bill_number;
                    document.getElementById('successGrandTotal').textContent = currency + result.grand_total.toFixed(2);

                    const changeAmount = Math.max(0, paidAmount - result.grand_total);
                    document.getElementById('successChangeAmount').textContent = currency + changeAmount.toFixed(2);
                    document.getElementById('successPrintLink').href = result.receipt_url;

                    document.getElementById('saleSuccessModal').classList.remove('hidden');

                    // Reset cart
                    cart = [];
                    renderCart();
                    document.getElementById('posDiscount').value = '0.00';
                    document.getElementById('posTax').value = '0.00';
                    document.getElementById('posPaidAmount').value = '0.00';
                    renderChange();

                    // Optional automatic receipt print popup
                    window.open(result.receipt_url, '_blank', 'width=420,height=600');
                } else {
                    alert('Checkout failed: ' + (result.message || 'Please check entries and stock levels.'));
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred during checkout. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>COMPLETE SALE & PRINT (F2)</span>
                `;
            }
        }

        function closeSuccessModal() {
            document.getElementById('saleSuccessModal').classList.add('hidden');
            if (searchInput) searchInput.focus();
        }

        // Quick add customer via API
        async function saveQuickCustomer(e) {
            e.preventDefault();
            const name = document.getElementById('qcName').value.trim();
            const phone = document.getElementById('qcPhone').value.trim();
            const email = document.getElementById('qcEmail').value.trim();

            if (!name) return;

            try {
                const formData = new FormData();
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('email', email);
                formData.append('_token', csrfToken);

                const response = await fetch('{{ route('customers.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'text/html' },
                    body: formData
                });

                // Add to customer select
                const select = document.getElementById('posCustomerId');
                const newOpt = document.createElement('option');
                newOpt.value = '';
                newOpt.textContent = '👤 ' + name;
                newOpt.setAttribute('data-name', name);
                newOpt.setAttribute('data-balance', 0);
                newOpt.selected = true;
                select.appendChild(newOpt);

                document.getElementById('posNewCustomerModal').classList.add('hidden');
                document.getElementById('quickCustomerForm').reset();
            } catch (err) {
                alert('Could not add customer.');
            }
        }

        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // F2: Complete Sale
            if (e.key === 'F2') {
                e.preventDefault();
                processCheckout();
            }
            // Escape: Close Modals
            if (e.key === 'Escape') {
                closeSuccessModal();
                document.getElementById('posNewCustomerModal').classList.add('hidden');
            }
        });
    </script>
</body>
</html>

