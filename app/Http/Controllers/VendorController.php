<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use App\Models\Vendor;
use App\Models\VendorPayment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vendor::withCount('bills');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->get('filter') === 'with_due') {
            $query->where('current_balance', '>', 0);
        }

        $vendors = $query->orderBy('name')->paginate(15)->withQueryString();
        $settings = ShopSetting::getAllSettings();

        $totalVendors = Vendor::count();
        $totalPayables = Vendor::where('current_balance', '>', 0)->sum('current_balance');

        return view('vendors.index', compact('vendors', 'settings', 'totalVendors', 'totalPayables'));
    }

    public function create(): View
    {
        $settings = ShopSetting::getAllSettings();
        return view('vendors.create', compact('settings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $opening = (float) ($validated['opening_balance'] ?? 0);

        Vendor::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('vendors.index')
            ->with('success', "Vendor '{$validated['name']}' added successfully.");
    }

    public function show(Vendor $vendor): View
    {
        $vendor->load(['bills' => function ($q) {
            $q->orderByDesc('bill_date')->orderByDesc('id');
        }, 'payments' => function ($q) {
            $q->orderByDesc('payment_date')->orderByDesc('id');
        }]);

        $settings = ShopSetting::getAllSettings();

        $totalPurchases = $vendor->bills->sum('grand_total');
        $totalPaid = $vendor->payments->sum('amount');

        return view('vendors.show', compact('vendor', 'settings', 'totalPurchases', 'totalPaid'));
    }

    public function edit(Vendor $vendor): View
    {
        $settings = ShopSetting::getAllSettings();
        return view('vendors.edit', compact('vendor', 'settings'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', "Vendor '{$vendor->name}' updated successfully.");
    }

    public function recordPayment(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,cheque,mobile'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'vendor_bill_id' => ['nullable', 'exists:vendor_bills,id'],
        ]);

        DB::transaction(function () use ($vendor, $validated) {
            $bill = null;
            if (!empty($validated['vendor_bill_id'])) {
                $bill = $vendor->bills()->find($validated['vendor_bill_id']);
            }

            VendorPayment::create([
                'vendor_id' => $vendor->id,
                'vendor_bill_id' => $bill ? $bill->id : null,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            if ($bill) {
                $bill->paid_amount += $validated['amount'];
                $bill->updatePaymentStatus();
            } else {
                $remaining = (float) $validated['amount'];
                $unpaidBills = $vendor->bills()
                    ->where('payment_status', '!=', 'paid')
                    ->orderBy('bill_date')
                    ->orderBy('id')
                    ->get();

                foreach ($unpaidBills as $uBill) {
                    if ($remaining <= 0) break;
                    $dueOnThis = (float) $uBill->due_amount;
                    if ($remaining >= $dueOnThis) {
                        $uBill->paid_amount += $dueOnThis;
                        $remaining -= $dueOnThis;
                    } else {
                        $uBill->paid_amount += $remaining;
                        $remaining = 0;
                    }
                    $uBill->updatePaymentStatus();
                }
            }

            $vendor->recalculateBalance();
        });

        return redirect()->back()
            ->with('success', "Payment of " . ShopSetting::get('currency_symbol', '$') . number_format($validated['amount'], 2) . " paid to {$vendor->name}.");
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        if ($vendor->bills()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot delete vendor '{$vendor->name}' because they have associated purchase bills.");
        }

        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', "Vendor removed successfully.");
    }
}

