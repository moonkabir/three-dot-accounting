<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBill;
use App\Models\CustomerBillItem;
use App\Models\CustomerPayment;
use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\VendorPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Sales within period
        $salesQuery = CustomerBill::whereBetween('bill_date', [$startDate, $endDate]);
        $totalSales = (clone $salesQuery)->sum('grand_total');
        $totalSalesPaid = (clone $salesQuery)->sum('paid_amount');
        $totalSalesDue = (clone $salesQuery)->sum('due_amount');
        $salesCount = (clone $salesQuery)->count();

        // 2. Purchases within period
        $purchaseQuery = VendorBill::whereBetween('bill_date', [$startDate, $endDate]);
        $totalPurchases = (clone $purchaseQuery)->sum('grand_total');
        $totalPurchasesPaid = (clone $purchaseQuery)->sum('paid_amount');
        $totalPurchasesDue = (clone $purchaseQuery)->sum('due_amount');
        $purchasesCount = (clone $purchaseQuery)->count();

        // 3. Cost of Goods Sold (COGS) for sales within period
        $cogs = CustomerBillItem::whereHas('customerBill', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('bill_date', [$startDate, $endDate]);
        })->select(DB::raw('SUM(unit_cost * quantity) as total_cogs'))->value('total_cogs') ?? 0;

        // Gross Profit = Total Sales - COGS
        $grossProfit = $totalSales - $cogs;
        $profitMargin = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0;

        // 4. Cash Flow within period
        $cashIn = CustomerPayment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $cashOut = VendorPayment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $netCashFlow = $cashIn - $cashOut;

        // 5. Overall Balances
        $totalCustomerReceivables = Customer::where('current_balance', '>', 0)->sum('current_balance');
        $totalVendorPayables = Vendor::where('current_balance', '>', 0)->sum('current_balance');

        // 6. Top Selling Products
        $topSellingItems = CustomerBillItem::whereHas('customerBill', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('bill_date', [$startDate, $endDate]);
        })->select('item_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
          ->groupBy('item_name')
          ->orderByDesc('total_revenue')
          ->limit(5)
          ->get();

        $settings = ShopSetting::getAllSettings();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalSalesPaid',
            'totalSalesDue',
            'salesCount',
            'totalPurchases',
            'totalPurchasesPaid',
            'totalPurchasesDue',
            'purchasesCount',
            'cogs',
            'grossProfit',
            'profitMargin',
            'cashIn',
            'cashOut',
            'netCashFlow',
            'totalCustomerReceivables',
            'totalVendorPayables',
            'topSellingItems',
            'settings'
        ));
    }

    public function cashBook(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $customerPayments = CustomerPayment::with('customer', 'bill')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get()
            ->map(function ($p) {
                return (object) [
                    'date' => $p->payment_date,
                    'type' => 'in',
                    'party' => $p->customer ? $p->customer->name : 'Walk-in',
                    'category' => 'Customer Receipt (' . ($p->bill ? "#{$p->bill->bill_number}" : 'Account') . ')',
                    'method' => $p->payment_method,
                    'reference' => $p->reference_number,
                    'inflow' => (float) $p->amount,
                    'outflow' => 0.0,
                    'notes' => $p->notes,
                ];
            });

        $vendorPayments = VendorPayment::with('vendor', 'bill')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get()
            ->map(function ($p) {
                return (object) [
                    'date' => $p->payment_date,
                    'type' => 'out',
                    'party' => $p->vendor ? $p->vendor->name : 'Supplier',
                    'category' => 'Vendor Payment (' . ($p->bill ? "#{$p->bill->bill_number}" : 'Account') . ')',
                    'method' => $p->payment_method,
                    'reference' => $p->reference_number,
                    'inflow' => 0.0,
                    'outflow' => (float) $p->amount,
                    'notes' => $p->notes,
                ];
            });

        $transactions = $customerPayments->concat($vendorPayments)
            ->sortByDesc('date')
            ->values();

        $totalInflow = $transactions->sum('inflow');
        $totalOutflow = $transactions->sum('outflow');
        $netBalance = $totalInflow - $totalOutflow;
        $settings = ShopSetting::getAllSettings();

        return view('reports.cash_book', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalInflow',
            'totalOutflow',
            'netBalance',
            'settings'
        ));
    }
}

