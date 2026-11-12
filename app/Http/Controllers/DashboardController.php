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

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Sales & Revenue
        $todaySales = CustomerBill::whereDate('bill_date', $today)->sum('grand_total');
        $todayBillsCount = CustomerBill::whereDate('bill_date', $today)->count();
        $monthSales = CustomerBill::whereBetween('bill_date', [$startOfMonth, $endOfMonth])->sum('grand_total');
        $totalSales = CustomerBill::sum('grand_total');

        // 2. Purchases & Expenses
        $todayPurchases = VendorBill::whereDate('bill_date', $today)->sum('grand_total');
        $monthPurchases = VendorBill::whereBetween('bill_date', [$startOfMonth, $endOfMonth])->sum('grand_total');
        $totalPurchases = VendorBill::sum('grand_total');

        // 3. Receivables & Payables
        $customerReceivables = CustomerBill::where('payment_status', '!=', 'paid')->sum('due_amount');
        $vendorPayables = VendorBill::where('payment_status', '!=', 'paid')->sum('due_amount');

        // 4. Cash Inflow & Outflow Today
        $todayCashIn = CustomerPayment::whereDate('payment_date', $today)->sum('amount');
        $todayCashOut = VendorPayment::whereDate('payment_date', $today)->sum('amount');
        $netCashFlowToday = $todayCashIn - $todayCashOut;

        // 5. Estimated Gross Profit (Month & All Time)
        // Profit = Sum of ((unit_price - unit_cost) * quantity)
        $monthProfit = CustomerBillItem::whereHas('customerBill', function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('bill_date', [$startOfMonth, $endOfMonth]);
        })->select(DB::raw('SUM((unit_price - unit_cost) * quantity) as gross_profit'))->value('gross_profit') ?? 0;

        $totalProfit = CustomerBillItem::select(DB::raw('SUM((unit_price - unit_cost) * quantity) as gross_profit'))
            ->value('gross_profit') ?? 0;

        // 6. Inventory Summary
        $totalItemsCount = Item::count();
        $lowStockItems = Item::whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->orderBy('stock_quantity', 'asc')
            ->get();
        $lowStockCount = $lowStockItems->count();

        $inventoryValuation = Item::select(DB::raw('SUM(stock_quantity * cost_price) as total_val'))
            ->value('total_val') ?? 0;

        // 7. Recent Sales & Purchase Bills
        $recentSales = CustomerBill::with('customer')
            ->orderByDesc('bill_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentPurchases = VendorBill::with('vendor')
            ->orderByDesc('bill_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $customersCount = Customer::count();
        $vendorsCount = Vendor::count();
        $settings = ShopSetting::getAllSettings();

        return view('dashboard.index', compact(
            'todaySales',
            'todayBillsCount',
            'monthSales',
            'totalSales',
            'todayPurchases',
            'monthPurchases',
            'totalPurchases',
            'customerReceivables',
            'vendorPayables',
            'todayCashIn',
            'todayCashOut',
            'netCashFlowToday',
            'monthProfit',
            'totalProfit',
            'totalItemsCount',
            'lowStockItems',
            'lowStockCount',
            'inventoryValuation',
            'recentSales',
            'recentPurchases',
            'customersCount',
            'vendorsCount',
            'settings'
        ));
    }
}

