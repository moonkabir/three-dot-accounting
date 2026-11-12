<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\ShopSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('bills');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->get('filter') === 'with_due') {
            $query->where('current_balance', '>', 0);
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();
        $settings = ShopSetting::getAllSettings();

        $totalCustomers = Customer::count();
        $totalReceivables = Customer::where('current_balance', '>', 0)->sum('current_balance');

        return view('customers.index', compact('customers', 'settings', 'totalCustomers', 'totalReceivables'));
    }

    public function create(): View
    {
        $settings = ShopSetting::getAllSettings();
        return view('customers.create', compact('settings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $opening = (float) ($validated['opening_balance'] ?? 0);

        Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('customers.index')
            ->with('success', "Customer '{$validated['name']}' added successfully.");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['bills' => function ($q) {
            $q->orderByDesc('bill_date')->orderByDesc('id');
        }, 'payments' => function ($q) {
            $q->orderByDesc('payment_date')->orderByDesc('id');
        }]);

        $settings = ShopSetting::getAllSettings();

        $totalBilled = $customer->bills->sum('grand_total');
        $totalPaid = $customer->payments->sum('amount');

        return view('customers.show', compact('customer', 'settings', 'totalBilled', 'totalPaid'));
    }

    public function edit(Customer $customer): View
    {
        $settings = ShopSetting::getAllSettings();
        return view('customers.edit', compact('customer', 'settings'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', "Customer '{$customer->name}' updated successfully.");
    }

    public function recordPayment(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,card,mobile'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'customer_bill_id' => ['nullable', 'exists:customer_bills,id'],
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $bill = null;
            if (!empty($validated['customer_bill_id'])) {
                $bill = $customer->bills()->find($validated['customer_bill_id']);
            }

            $payment = CustomerPayment::create([
                'customer_id' => $customer->id,
                'customer_bill_id' => $bill ? $bill->id : null,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // If a specific bill was selected, update its paid_amount and status
            if ($bill) {
                $bill->paid_amount += $validated['amount'];
                $bill->updatePaymentStatus();
            } else {
                // Apply to oldest unpaid bills sequentially
                $remaining = (float) $validated['amount'];
                $unpaidBills = $customer->bills()
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

            $customer->recalculateBalance();
        });

        return redirect()->back()
            ->with('success', "Payment of " . ShopSetting::get('currency_symbol', '$') . number_format($validated['amount'], 2) . " recorded successfully.");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->bills()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot delete customer '{$customer->name}' because they have associated sales bills.");
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', "Customer removed successfully.");
    }
}

