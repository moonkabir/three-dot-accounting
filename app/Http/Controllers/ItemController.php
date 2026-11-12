<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with('category');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->get('filter') === 'low_stock') {
            $query->whereColumn('stock_quantity', '<=', 'alert_quantity');
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();

        $totalItems = Item::count();
        $lowStockCount = Item::whereColumn('stock_quantity', '<=', 'alert_quantity')->count();
        $totalValuation = Item::select(DB::raw('SUM(stock_quantity * cost_price) as val'))->value('val') ?? 0;

        return view('inventory.index', compact(
            'items',
            'categories',
            'settings',
            'totalItems',
            'lowStockCount',
            'totalValuation'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();
        $suggestedSku = 'ITEM-' . str_pad(Item::max('id') + 1, 4, '0', STR_PAD_LEFT);

        return view('inventory.create', compact('categories', 'settings', 'suggestedSku'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:items,sku'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'numeric', 'min:0'],
            'alert_quantity' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::create([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'category_id' => !empty($validated['category_id']) ? $validated['category_id'] : null,
                'unit' => strtolower($validated['unit']),
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock_quantity' => $validated['stock_quantity'],
                'alert_quantity' => $validated['alert_quantity'] ?? 5,
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            if ($item->stock_quantity > 0) {
                StockAdjustment::create([
                    'item_id' => $item->id,
                    'type' => 'in',
                    'quantity' => $item->stock_quantity,
                    'unit_cost' => $item->cost_price,
                    'reason' => 'Initial stock on item creation',
                    'reference_type' => 'manual',
                    'reference_id' => null,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('inventory.index')
            ->with('success', "Item '{$validated['name']}' created successfully.");
    }

    public function edit(Item $inventory): View
    {
        $item = $inventory;
        $categories = Category::orderBy('name')->get();
        $settings = ShopSetting::getAllSettings();

        return view('inventory.edit', compact('item', 'categories', 'settings'));
    }

    public function update(Request $request, Item $inventory): RedirectResponse
    {
        $item = $inventory;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:items,sku,' . $item->id],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'alert_quantity' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $item->update([
            'name' => $validated['name'],
            'sku' => strtoupper($validated['sku']),
            'category_id' => !empty($validated['category_id']) ? $validated['category_id'] : null,
            'unit' => strtolower($validated['unit']),
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'alert_quantity' => $validated['alert_quantity'] ?? 5,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('inventory.index')
            ->with('success', "Item '{$item->name}' updated successfully.");
    }

    public function adjustStock(Request $request, Item $inventory): RedirectResponse
    {
        $item = $inventory;

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:add,subtract,set'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($item, $validated) {
            $currentStock = $item->stock_quantity;
            $qty = (float) $validated['quantity'];

            if ($validated['adjustment_type'] === 'add') {
                $newStock = $currentStock + $qty;
                $adjType = 'in';
                $adjQty = $qty;
            } elseif ($validated['adjustment_type'] === 'subtract') {
                $newStock = max(0, $currentStock - $qty);
                $adjType = 'out';
                $adjQty = $qty;
            } else { // set
                $newStock = $qty;
                $diff = $qty - $currentStock;
                $adjType = $diff >= 0 ? 'in' : 'out';
                $adjQty = abs($diff);
            }

            $item->stock_quantity = $newStock;
            $item->save();

            StockAdjustment::create([
                'item_id' => $item->id,
                'type' => $adjType,
                'quantity' => $adjQty,
                'unit_cost' => $item->cost_price,
                'reason' => $validated['reason'],
                'reference_type' => 'manual',
                'reference_id' => null,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->back()
            ->with('success', "Stock for '{$item->name}' adjusted to {$item->stock_quantity} {$item->unit}.");
    }

    public function destroy(Item $inventory): RedirectResponse
    {
        $item = $inventory;

        if ($item->customerBillItems()->exists() || $item->vendorBillItems()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot delete '{$item->name}' because it is linked to existing sales or purchase bills. You can mark it inactive instead.");
        }

        $item->delete();
        return redirect()->route('inventory.index')
            ->with('success', "Item deleted successfully.");
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', "Category '{$validated['name']}' created successfully.");
    }
}
