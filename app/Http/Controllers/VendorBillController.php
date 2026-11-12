<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\StockAdjustment;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Models\VendorPayment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorBillController extends Controller
{
    public function index(Request $request): View
    {
        $query = VendorBill::with('vendor');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('vendor_reference_no', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('bill_date', [$request->get('start_date'), $request->get('end_date')]);
        }

        $bills = $query->orderByDesc('bill_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();

        $totalPurchasesAmount = VendorBill::sum('grand_total');
        $totalPaidAmount = VendorBill::sum('paid_amount');
        $totalDueAmount = VendorBill::sum('due_amount');

        return view('vendor_bills.index', compact(
            'bills',
            'vendors',
            'settings',
            'totalPurchasesAmount',
            'totalPaidAmount',
            'totalDueAmount'
        ));
    }

    public function create(): View
    {
        $vendors = Vendor::orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();
        $nextBillNumber = VendorBill::generateNextBillNumber();
        $today = Carbon::today()->toDateString();

        return view('vendor_bills.create', compact('vendors', 'items', 'settings', 'nextBillNumber', 'today'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bill_number' => ['required', 'string', 'max:50', 'unique:vendor_bills,bill_number'],
            'vendor_reference_no' => ['nullable', 'string', 'max:100'],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque,credit'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $bill = DB::transaction(function () use ($validated) {
            $vendor = Vendor::findOrFail($validated['vendor_id']);

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

            $bill = VendorBill::create([
                'bill_number' => $validated['bill_number'],
                'vendor_reference_no' => $validated['vendor_reference_no'] ?? null,
                'vendor_id' => $vendor->id,
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

            // Save line items, increase stock, and update cost price
            foreach ($itemsData as $data) {
                VendorBillItem::create([
                    'vendor_bill_id' => $bill->id,
                    'item_id' => $data['item']->id,
                    'item_name' => $data['item']->name,
                    'quantity' => $data['quantity'],
                    'unit_price' => $data['unit_price'],
                    'total_price' => $data['total_price'],
                ]);

                // Increase inventory stock & optionally update cost price
                $data['item']->stock_quantity += $data['quantity'];
                $data['item']->cost_price = $data['unit_price']; // update to latest purchase cost
                $data['item']->save();

                StockAdjustment::create([
                    'item_id' => $data['item']->id,
                    'type' => 'in',
                    'quantity' => $data['quantity'],
                    'unit_cost' => $data['unit_price'],
                    'reason' => "Purchase Bill #{$bill->bill_number} from {$vendor->name}",
                    'reference_type' => 'vendor_bill',
                    'reference_id' => $bill->id,
                    'user_id' => Auth::id(),
                ]);
            }

            // If initial paid_amount > 0, record vendor payment
            if ($paidAmount > 0) {
                VendorPayment::create([
                    'vendor_id' => $vendor->id,
                    'vendor_bill_id' => $bill->id,
                    'payment_date' => $bill->bill_date,
                    'amount' => $paidAmount,
                    'payment_method' => $bill->payment_method,
                    'reference_number' => "PUR-PAY-{$bill->id}",
                    'notes' => "Payment made during purchase creation for Bill #{$bill->bill_number}",
                    'user_id' => Auth::id(),
                ]);
            }

            $vendor->recalculateBalance();

            return $bill;
        });

        return redirect()->route('vendor-bills.show', $bill)
            ->with('success', "Vendor Purchase Bill #{$bill->bill_number} created and inventory updated.");
    }

    public function show(VendorBill $vendorBill): View
    {
        $vendorBill->load(['vendor', 'items.item', 'payments']);
        $settings = ShopSetting::getAllSettings();

        return view('vendor_bills.show', compact('vendorBill', 'settings'));
    }

    public function printInvoice(VendorBill $vendorBill): View
    {
        $vendorBill->load(['vendor', 'items.item', 'payments']);
        $settings = ShopSetting::getAllSettings();

        return view('vendor_bills.print', compact('vendorBill', 'settings'));
    }

    public function recordPayment(Request $request, VendorBill $vendorBill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $vendorBill->due_amount],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,cheque,mobile'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($vendorBill, $validated) {
            $paymentAmount = (float) $validated['amount'];

            $vendorBill->paid_amount += $paymentAmount;
            $vendorBill->updatePaymentStatus();

            VendorPayment::create([
                'vendor_id' => $vendorBill->vendor_id,
                'vendor_bill_id' => $vendorBill->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? "Payment for Bill #{$vendorBill->bill_number}",
                'user_id' => Auth::id(),
            ]);

            $vendorBill->vendor->recalculateBalance();
        });

        return redirect()->back()
            ->with('success', "Payment of " . ShopSetting::get('currency_symbol', '$') . number_format($validated['amount'], 2) . " paid towards Vendor Bill #{$vendorBill->bill_number}.");
    }

    public function destroy(VendorBill $vendorBill): RedirectResponse
    {
        DB::transaction(function () use ($vendorBill) {
            // Deduct stock back
            foreach ($vendorBill->items as $itemRow) {
                if ($itemRow->item) {
                    $itemRow->item->stock_quantity = max(0, $itemRow->item->stock_quantity - $itemRow->quantity);
                    $itemRow->item->save();

                    StockAdjustment::create([
                        'item_id' => $itemRow->item->id,
                        'type' => 'out',
                        'quantity' => $itemRow->quantity,
                        'unit_cost' => $itemRow->unit_price,
                        'reason' => "Voided/Deleted Purchase Bill #{$vendorBill->bill_number}",
                        'reference_type' => 'manual',
                        'reference_id' => null,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $vendor = $vendorBill->vendor;
            $vendorBill->delete();

            if ($vendor) {
                $vendor->recalculateBalance();
            }
        });

        return redirect()->route('vendor-bills.index')
            ->with('success', "Purchase Bill #{$vendorBill->bill_number} deleted and inventory stock adjusted.");
    }
}

