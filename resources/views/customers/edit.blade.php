@extends('layouts.app')

@section('title', 'Edit ' . $customer->name)
@section('page_heading', 'Edit Customer Information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Edit Customer: {{ $customer->name }}</h2>
                <p class="text-xs text-slate-500">Update contact and address information</p>
            </div>
            <a href="{{ route('customers.show', $customer) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; View Ledger
            </a>
        </div>

        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-semibold text-slate-700 mb-1">Customer / Organization Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label for="email" class="block font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>
            </div>

            <div>
                <label for="address" class="block font-semibold text-slate-700 mb-1">Billing / Street Address</label>
                <input type="text" id="address" name="address" value="{{ old('address', $customer->address) }}"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <div>
                <label for="notes" class="block font-semibold text-slate-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">{{ old('notes', $customer->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/30">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

