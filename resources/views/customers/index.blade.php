@extends('layouts.app')

@section('title', 'Customers')
@section('page_heading', 'Customer & Client Management')

@section('content')
<div class="space-y-6">
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Customers</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalCustomers }}</div>
                <div class="text-xs text-slate-500 mt-1">Registered clients & accounts</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Customer Receivables</span>
                <div class="text-2xl font-black text-blue-700 mt-1">
                    {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($totalReceivables, 2) }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Unpaid customer invoices to collect</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Search & Filters -->
            <form method="GET" action="{{ route('customers.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[220px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone or email..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <a href="{{ route('customers.index', array_merge(request()->except('filter'), request('filter') === 'with_due' ? [] : ['filter' => 'with_due'])) }}"
                   class="px-3 py-2 rounded-xl text-xs font-medium border transition-colors {{ request('filter') === 'with_due' ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                    Unpaid Balance Only
                </a>

                @if (request()->hasAny(['search', 'filter']))
                    <a href="{{ route('customers.index') }}" class="text-xs text-indigo-600 hover:underline">Reset</a>
                @endif
            </form>

            <a href="{{ route('customers.create') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Add Customer</span>
            </a>
        </div>

        <!-- Customers Table -->
        <div class="overflow-x-auto border-t border-slate-100 pt-3">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">Customer Name</th>
                        <th class="py-3 px-3">Contact</th>
                        <th class="py-3 px-3">Invoices Count</th>
                        <th class="py-3 px-3">Outstanding Balance</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $cust)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <a href="{{ route('customers.show', $cust) }}" class="hover:text-indigo-600 font-bold">
                                    {{ $cust->name }}
                                </a>
                                @if ($cust->address)
                                    <div class="text-[11px] text-slate-400 font-normal truncate max-w-xs">{{ $cust->address }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                @if ($cust->phone)
                                    <div class="font-medium text-slate-800">{{ $cust->phone }}</div>
                                @endif
                                @if ($cust->email)
                                    <div class="text-slate-400 text-[11px]">{{ $cust->email }}</div>
                                @endif
                                @if (!$cust->phone && !$cust->email)
                                    <span class="text-slate-400 italic">No contact info</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-700">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold">{{ $cust->bills_count }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if ($cust->current_balance > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        Due: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($cust->current_balance, 2) }}
                                    </span>
                                @elseif ($cust->current_balance < 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Advance: {{ $settings['currency_symbol'] ?? '$' }}{{ number_format(abs($cust->current_balance), 2) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Settled ($0.00)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('customers.show', $cust) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-xs transition-colors">
                                    Ledger
                                </a>
                                <a href="{{ route('customers.edit', $cust) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $cust) }}" class="inline" onsubmit="return confirm('Delete customer?');">
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
                            <td colspan="5" class="py-8 text-center text-slate-400">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection

