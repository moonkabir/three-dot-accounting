<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBill;
use App\Models\CustomerBillItem;
use App\Models\CustomerPayment;
use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\StockAdjustment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerBillController extends Controller
{
    public function index(Request $request): View
    {
        $query = CustomerBill::with('customer');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('bill_date', [$request->get('start_date'), $request->get('end_date')]);
        }

        $bills = $query->orderByDesc('bill_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();

        $totalSalesAmount = CustomerBill::sum('grand_total');
        $totalPaidAmount = CustomerBill::sum('paid_amount');
        $totalDueAmount = CustomerBill::sum('due_amount');

        return view('customer_bills.index', compact(
            'bills',
            'customers',
            'settings',
            'totalSalesAmount',
            'totalPaidAmount',
            'totalDueAmount'
        ));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();
        $nextBillNumber = CustomerBill::generateNextBillNumber();
        $today = Carbon::today()->toDateString();

        return view('customer_bills.create', compact('customers', 'items', 'settings', 'nextBillNumber', 'today'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bill_number' => ['required', 'string', 'max:50', 'unique:customer_bills,bill_number'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,mobile_banking,credit'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $bill = DB::transaction(function () use ($validated) {
            $customer = null;
            $customerName = $validated['customer_name'] ?? 'Walk-in Customer';

            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
                if ($customer) {
                    $customerName = $customer->name;
                }
            }

            // Calculate subtotal
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemRow) {
                $item = Item::lockForUpdate()->find($itemRow['item_id']);
                $qty = (float) $itemRow['quantity'];
                $unitPrice = (float) $itemRow['unit_price'];
                $lineTotal = round($qty * $unitPrice, 2);
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'item' => $item,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $item->cost_price,
                    'total_price' => $lineTotal,
                ];
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);
            $grandTotal = max(0, round($subtotal - $discount + $tax, 2));
            $paidAmount = min($grandTotal, (float) ($validated['paid_amount'] ?? 0));
            $dueAmount = round($grandTotal - $paidAmount, 2);

            $paymentStatus = 'unpaid';
            if ($paidAmount >= $grandTotal && $grandTotal > 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            // Create bill
            $bill = CustomerBill::create([
                'bill_number' => $validated['bill_number'],
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customerName,
                'bill_date' => $validated['bill_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // Save line items & adjust stock
            foreach ($itemsData as $data) {
                CustomerBillItem::create([
                    'customer_bill_id' => $bill->id,
                    'item_id' => $data['item']->id,
                    'item_name' => $data['item']->name,
                    'quantity' => $data['quantity'],
                    'unit_price' => $data['unit_price'],
                    'unit_cost' => $data['unit_cost'],
                    'total_price' => $data['total_price'],
                ]);

                // Reduce inventory stock
                $data['item']->stock_quantity = max(0, $data['item']->stock_quantity - $data['quantity']);
                $data['item']->save();

                StockAdjustment::create([
                    'item_id' => $data['item']->id,
                    'type' => 'out',
                    'quantity' => $data['quantity'],
                    'unit_cost' => $data['unit_cost'],
                    'reason' => "Sale Bill #{$bill->bill_number}",
                    'reference_type' => 'customer_bill',
                    'reference_id' => $bill->id,
                    'user_id' => Auth::id(),
                ]);
            }

            // Log customer payment if paid > 0
            if ($paidAmount > 0) {
                CustomerPayment::create([
                    'customer_id' => $customer ? $customer->id : null,
                    'customer_bill_id' => $bill->id,
                    'payment_date' => $bill->bill_date,
                    'amount' => $paidAmount,
                    'payment_method' => $bill->payment_method,
                    'reference_number' => "BILL-PAY-{$bill->id}",
                    'notes' => "Payment made during checkout of Bill #{$bill->bill_number}",
                    'user_id' => Auth::id(),
                ]);
            }

            if ($customer) {
                $customer->recalculateBalance();
            }

            return $bill;
        });

        return redirect()->route('customer-bills.show', $bill)
            ->with('success', "Customer Bill #{$bill->bill_number} created successfully.");
    }

    public function show(CustomerBill $customerBill): View
    {
        $customerBill->load(['customer', 'items.item', 'payments']);
        $settings = ShopSetting::getAllSettings();

        return view('customer_bills.show', compact('customerBill', 'settings'));
    }

    public function printInvoice(CustomerBill $customerBill): View
    {
        $customerBill->load(['customer', 'items.item', 'payments']);
        $settings = ShopSetting::getAllSettings();

        return view('customer_bills.print', compact('customerBill', 'settings'));
    }

    public function recordPayment(Request $request, CustomerBill $customerBill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $customerBill->due_amount],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,bank,mobile'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($customerBill, $validated) {
            $paymentAmount = (float) $validated['amount'];

            $customerBill->paid_amount += $paymentAmount;
            $customerBill->updatePaymentStatus();

            CustomerPayment::create([
                'customer_id' => $customerBill->customer_id ?? 0,
                'customer_bill_id' => $customerBill->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? "Payment for Bill #{$customerBill->bill_number}",
                'user_id' => Auth::id(),
            ]);

            if ($customerBill->customer) {
                $customerBill->customer->recalculateBalance();
            }
        });

        return redirect()->back()
            ->with('success', "Payment of " . ShopSetting::get('currency_symbol', '$') . number_format($validated['amount'], 2) . " recorded on Bill #{$customerBill->bill_number}.");
    }

    public function destroy(CustomerBill $customerBill): RedirectResponse
    {
        DB::transaction(function () use ($customerBill) {
            // Restore inventory stock
            foreach ($customerBill->items as $itemRow) {
                if ($itemRow->item) {
                    $itemRow->item->stock_quantity += $itemRow->quantity;
                    $itemRow->item->save();

                    StockAdjustment::create([
                        'item_id' => $itemRow->item->id,
                        'type' => 'in',
                        'quantity' => $itemRow->quantity,
                        'unit_cost' => $itemRow->unit_cost,
                        'reason' => "Voided/Deleted Sale Bill #{$customerBill->bill_number}",
                        'reference_type' => 'manual',
                        'reference_id' => null,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $customer = $customerBill->customer;
            $customerBill->delete();

            if ($customer) {
                $customer->recalculateBalance();
            }
        });

        return redirect()->route('customer-bills.index')
            ->with('success', "Bill #{$customerBill->bill_number} deleted and stock restored.");
    }
}

