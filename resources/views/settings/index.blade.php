@extends('layouts.app')

@section('title', 'Shop Settings')
@section('page_heading', 'Shop & Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Shop Information Form -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Store Profile & Invoice Information</h2>
            <p class="text-xs text-slate-500">This information will appear on printed invoices, receipts and reports</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="shop_name" class="block font-semibold text-slate-700 mb-1">Shop / Business Name *</label>
                    <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name', $settings['shop_name'] ?? 'Three Dot Accounting') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-bold text-slate-800">
                </div>

                <div>
                    <label for="shop_tagline" class="block font-semibold text-slate-700 mb-1">Shop Tagline</label>
                    <input type="text" id="shop_tagline" name="shop_tagline" value="{{ old('shop_tagline', $settings['shop_tagline'] ?? '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="e.g. Wholesale & Retail Mart">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="shop_phone" class="block font-semibold text-slate-700 mb-1">Shop Contact Phone</label>
                    <input type="text" id="shop_phone" name="shop_phone" value="{{ old('shop_phone', $settings['shop_phone'] ?? '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="+1 (555) 012-3456">
                </div>

                <div>
                    <label for="shop_email" class="block font-semibold text-slate-700 mb-1">Store Email</label>
                    <input type="email" id="shop_email" name="shop_email" value="{{ old('shop_email', $settings['shop_email'] ?? '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="shop@example.com">
                </div>

                <div>
                    <label for="currency_symbol" class="block font-semibold text-slate-700 mb-1">Currency Symbol *</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-bold font-mono">
                    <span class="text-[10px] text-slate-400 mt-1 block">e.g. $, ৳, €, £, ₹</span>
                </div>
            </div>

            <div>
                <label for="shop_address" class="block font-semibold text-slate-700 mb-1">Physical Store Address</label>
                <input type="text" id="shop_address" name="shop_address" value="{{ old('shop_address', $settings['shop_address'] ?? '') }}"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="Street, Market, Area">
            </div>

            <div>
                <label for="invoice_terms" class="block font-semibold text-slate-700 mb-1">Invoice Receipt Terms & Return Policy</label>
                <textarea id="invoice_terms" name="invoice_terms" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">{{ old('invoice_terms', $settings['invoice_terms'] ?? '') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/30">
                    Save Shop Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Security & Password Update -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Change Account Password</h2>
            <p class="text-xs text-slate-500">Logged in as: <strong class="text-slate-800">{{ $user->email }}</strong></p>
        </div>

        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4 text-xs max-w-md">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block font-semibold text-slate-700 mb-1">Current Password *</label>
                <input type="password" id="current_password" name="current_password" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <div>
                <label for="password" class="block font-semibold text-slate-700 mb-1">New Password *</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs" placeholder="At least 6 characters">
            </div>

            <div>
                <label for="password_confirmation" class="block font-semibold text-slate-700 mb-1">Confirm New Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs shadow-md">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

