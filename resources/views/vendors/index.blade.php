@extends('layouts.app')

@section('title', 'Vendors & Suppliers')
@section('page_heading', 'Vendor & Supplier Management')

@section('content')
<div class="space-y-6">
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Suppliers / Vendors</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalVendors }}</div>
                <div class="text-xs text-slate-500 mt-1">Wholesalers & distributors</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Vendor Payables</span>
                <div class="text-2xl font-black text-amber-700 mt-1">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalPayables, 2) }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Outstanding bills owed to suppliers</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('vendors.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[220px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, company, or phone..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <a href="{{ route('vendors.index', array_merge(request()->except('filter'), request('filter') === 'with_due' ? [] : ['filter' => 'with_due'])) }}"
                   class="px-3 py-2 rounded-xl text-xs font-medium border transition-colors {{ request('filter') === 'with_due' ? 'bg-amber-50 border-amber-300 text-amber-800 font-bold' : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                    Unpaid Payables Only
                </a>

                @if (request()->hasAny(['search', 'filter']))
                    <a href="{{ route('vendors.index') }}" class="text-xs text-amber-700 hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('vendors.create') }}" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Add Vendor</span>
            </a>
        </div>

        <!-- Vendors Table -->
        <div class="overflow-x-auto border-t border-slate-100 pt-3">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">Vendor / Company</th>
                        <th class="py-3 px-3">Contact</th>
                        <th class="py-3 px-3">Bills Count</th>
                        <th class="py-3 px-3">Outstanding Payable</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($vendors as $vendor)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <a href="{{ route('vendors.show', $vendor) }}" class="hover:text-amber-700 font-bold">
                                    {{ $vendor->name }}
                                </a>
                                @if ($vendor->company_name)
                                    <div class="text-[11px] text-slate-500 font-normal">{{ $vendor->company_name }}</div>
                                @endif
                                @if ($vendor->address)
                                    <div class="text-[11px] text-slate-400 font-normal truncate max-w-xs">{{ $vendor->address }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                @if ($vendor->phone)
                                    <div class="font-medium text-slate-800">{{ $vendor->phone }}</div>
                                @endif
                                @if ($vendor->email)
                                    <div class="text-slate-400 text-[11px]">{{ $vendor->email }}</div>
                                @endif
                                @if (!$vendor->phone && !$vendor->email)
                                    <span class="text-slate-400 italic">No contact info</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-700">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold">{{ $vendor->bills_count }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if ($vendor->current_balance > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900">
                                        Payable: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($vendor->current_balance, 2) }}
                                    </span>
                                @elseif ($vendor->current_balance < 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Advance Paid: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format(abs($vendor->current_balance), 2) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Settled ($0.00)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('vendors.show', $vendor) }}" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-medium text-xs transition-colors">
                                    Ledger
                                </a>
                                <a href="{{ route('vendors.edit', $vendor) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline" onsubmit="return confirm('Delete vendor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">No vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection

