@extends('layouts.app')

@section('title', 'Add Customer')
@section('page_heading', 'Register New Customer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Customer Details</h2>
                <p class="text-xs text-slate-500">Add a new customer account to track sales and credit</p>
            </div>
            <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Back to Customers
            </a>
        </div>

        <form method="POST" action="{{ route('customers.store') }}" class="space-y-5 text-xs">
            @csrf

            <div>
                <label for="name" class="block font-semibold text-slate-700 mb-1">Customer / Organization Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. John Doe or Green Valley Cafe">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. +1 (555) 012-3456">
                </div>

                <div>
                    <label for="email" class="block font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. customer@example.com">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="opening_balance" class="block font-semibold text-slate-700 mb-1">Opening Due Balance (if any)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400">{{ $settings['currency_symbol'] ?? '$' }}</span>
                        <input type="number" step="0.01" id="opening_balance" name="opening_balance" value="{{ old('opening_balance', '0.00') }}"
                            class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Previous unpaid balance owed to your shop</p>
                </div>

                <div>
                    <label for="address" class="block font-semibold text-slate-700 mb-1">Billing / Street Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="Street, city, area">
                </div>
            </div>

            <div>
                <label for="notes" class="block font-semibold text-slate-700 mb-1">Customer Notes / Payment Terms</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. 15-day payment terms, regular buyer...">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/30">
                    Save Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

