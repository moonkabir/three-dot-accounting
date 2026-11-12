<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerBill;
use App\Models\CustomerBillItem;
use App\Models\CustomerPayment;
use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\StockAdjustment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['items' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('name')->get();

        $items = Item::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $customers = Customer::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();
        $nextBillNumber = CustomerBill::generateNextBillNumber();
        $today = Carbon::today()->toDateString();

        return view('pos.index', compact('categories', 'items', 'customers', 'settings', 'nextBillNumber', 'today'));
    }

    public function checkout(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
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

            // Calculate subtotal and prepare items
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

            $paidReceived = (float) ($validated['paid_amount'] ?? 0);
            // Amount credited against this bill is capped at grandTotal
            $paidAmount = min($grandTotal, $paidReceived);
            $dueAmount = round($grandTotal - $paidAmount, 2);

            $paymentStatus = 'unpaid';
            if ($paidAmount >= $grandTotal && $grandTotal > 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            $billNumber = CustomerBill::generateNextBillNumber();

            // Create customer bill
            $bill = CustomerBill::create([
                'bill_number' => $billNumber,
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customerName,
                'bill_date' => Carbon::today()->toDateString(),
                'due_date' => null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? 'POS Terminal Sale',
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
                    'reason' => "POS Sale #{$bill->bill_number}",
                    'reference_type' => 'customer_bill',
                    'reference_id' => $bill->id,
                    'user_id' => Auth::id(),
                ]);
            }

            // Record customer payment
            if ($paidAmount > 0) {
                CustomerPayment::create([
                    'customer_id' => $customer ? $customer->id : null,
                    'customer_bill_id' => $bill->id,
                    'payment_date' => $bill->bill_date,
                    'amount' => $paidAmount,
                    'payment_method' => $bill->payment_method,
                    'reference_number' => "POS-PAY-{$bill->id}",
                    'notes' => "POS Checkout for Bill #{$bill->bill_number}",
                    'user_id' => Auth::id(),
                ]);
            }

            if ($customer) {
                $customer->recalculateBalance();
            }

            return $bill;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sale completed successfully! Bill #{$bill->bill_number}",
                'bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'grand_total' => $bill->grand_total,
                'paid_amount' => $bill->paid_amount,
                'due_amount' => $bill->due_amount,
                'receipt_url' => route('pos.receipt', $bill),
                'next_bill_number' => CustomerBill::generateNextBillNumber(),
            ]);
        }

        return redirect()->route('pos.receipt', $bill)
            ->with('success', "Sale #{$bill->bill_number} completed!");
    }

    public function receipt(CustomerBill $customerBill): View
    {
        $customerBill->load(['customer', 'items.item', 'payments']);
        $settings = ShopSetting::getAllSettings();

        return view('pos.receipt', compact('customerBill', 'settings'));
    }
}
