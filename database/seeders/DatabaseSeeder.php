<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerBill;
use App\Models\CustomerBillItem;
use App\Models\CustomerPayment;
use App\Models\Item;
use App\Models\ShopSetting;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Models\VendorPayment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@threedot.com'],
            [
                'name' => 'Shop Admin',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@threedot.com'],
            [
                'name' => 'Cashier Desk',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Shop Settings
        $settings = [
            'shop_name' => 'Three Dot Mart & Accounting',
            'shop_tagline' => 'Smart Retail & Inventory Management',
            'shop_phone' => '+1 (555) 782-9900',
            'shop_email' => 'store@threedotmart.com',
            'shop_address' => 'Shop #12, Central Avenue, Commercial District',
            'currency_symbol' => '$',
            'invoice_terms' => 'Thank you for shopping with us! In case of defects, returns are accepted within 7 days with valid receipt.',
        ];

        foreach ($settings as $key => $value) {
            ShopSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 3. Categories
        $catBeverages = Category::firstOrCreate(['name' => 'Beverages & Drinks'], ['description' => 'Cold drinks, juices, soda, water']);
        $catGroceries = Category::firstOrCreate(['name' => 'Groceries & Staples'], ['description' => 'Rice, flour, oil, spices, daily staples']);
        $catSnacks = Category::firstOrCreate(['name' => 'Snacks & Bakery'], ['description' => 'Biscuits, chips, chocolates, bread']);
        $catHousehold = Category::firstOrCreate(['name' => 'Household & Hygiene'], ['description' => 'Soaps, detergents, cleaning products']);
        $catStationery = Category::firstOrCreate(['name' => 'Stationery & Office'], ['description' => 'Pens, paper, notebooks, tape']);

        // 4. Products / Items
        $itemsData = [
            [
                'category_id' => $catBeverages->id,
                'sku' => 'BEV-001',
                'name' => 'Fresh Orange Juice (1L)',
                'unit' => 'bottle',
                'cost_price' => 2.20,
                'selling_price' => 3.50,
                'stock_quantity' => 45,
                'alert_quantity' => 10,
                'description' => '100% pure squeezed chilled orange juice',
            ],
            [
                'category_id' => $catBeverages->id,
                'sku' => 'BEV-002',
                'name' => 'Sparkling Mineral Water (500ml)',
                'unit' => 'bottle',
                'cost_price' => 0.60,
                'selling_price' => 1.20,
                'stock_quantity' => 120,
                'alert_quantity' => 20,
                'description' => 'Natural mineral carbonated water',
            ],
            [
                'category_id' => $catBeverages->id,
                'sku' => 'BEV-003',
                'name' => 'Organic Green Tea Pack (50 bags)',
                'unit' => 'box',
                'cost_price' => 4.50,
                'selling_price' => 7.00,
                'stock_quantity' => 4, // LOW STOCK ALERT!
                'alert_quantity' => 8,
                'description' => 'Antioxidant rich green tea bags',
            ],
            [
                'category_id' => $catGroceries->id,
                'sku' => 'GRO-001',
                'name' => 'Basmati Premium Rice (5kg)',
                'unit' => 'bag',
                'cost_price' => 8.50,
                'selling_price' => 12.00,
                'stock_quantity' => 28,
                'alert_quantity' => 10,
                'description' => 'Long grain aged aromatic rice',
            ],
            [
                'category_id' => $catGroceries->id,
                'sku' => 'GRO-002',
                'name' => 'Refined Sunflower Oil (2L)',
                'unit' => 'bottle',
                'cost_price' => 4.80,
                'selling_price' => 6.50,
                'stock_quantity' => 3, // LOW STOCK ALERT!
                'alert_quantity' => 10,
                'description' => 'Pure light cooking oil',
            ],
            [
                'category_id' => $catSnacks->id,
                'sku' => 'SNK-001',
                'name' => 'Artisan Dark Chocolate (100g)',
                'unit' => 'bar',
                'cost_price' => 1.80,
                'selling_price' => 3.00,
                'stock_quantity' => 60,
                'alert_quantity' => 15,
                'description' => '70% cocoa single origin chocolate',
            ],
            [
                'category_id' => $catSnacks->id,
                'sku' => 'SNK-002',
                'name' => 'Salted Roasted Almonds (250g)',
                'unit' => 'pack',
                'cost_price' => 3.50,
                'selling_price' => 5.50,
                'stock_quantity' => 22,
                'alert_quantity' => 5,
                'description' => 'Crunchy oven-roasted almonds',
            ],
            [
                'category_id' => $catHousehold->id,
                'sku' => 'HOU-001',
                'name' => 'Antibacterial Hand Wash (500ml)',
                'unit' => 'bottle',
                'cost_price' => 1.90,
                'selling_price' => 3.20,
                'stock_quantity' => 35,
                'alert_quantity' => 10,
                'description' => 'Moisturizing aloe vera hand wash',
            ],
            [
                'category_id' => $catStationery->id,
                'sku' => 'STA-001',
                'name' => 'A4 Executive Notebook (200 pages)',
                'unit' => 'pcs',
                'cost_price' => 2.00,
                'selling_price' => 4.00,
                'stock_quantity' => 2, // LOW STOCK ALERT!
                'alert_quantity' => 5,
                'description' => 'Hardcover ruled notebook',
            ],
        ];

        $items = [];
        foreach ($itemsData as $data) {
            $items[$data['sku']] = Item::firstOrCreate(['sku' => $data['sku']], $data);
        }

        // 5. Vendors (Suppliers)
        $vendor1 = Vendor::firstOrCreate(
            ['name' => 'Apex Wholesale Distributors'],
            [
                'company_name' => 'Apex Distro Group',
                'phone' => '+1 (555) 234-5678',
                'email' => 'sales@apexdistro.com',
                'address' => 'Building B, Industrial Park East, Cityville',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Primary FMCG and beverage supplier with 14-day credit terms',
            ]
        );

        $vendor2 = Vendor::firstOrCreate(
            ['name' => 'Global Agro Supplies'],
            [
                'company_name' => 'Global Agro Ltd',
                'phone' => '+1 (555) 345-6789',
                'email' => 'orders@globalagro.com',
                'address' => '78 Farmers Way, Grain Terminal, Port Town',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Bulk grains, pulses, and edible oil wholesaler',
            ]
        );

        $vendor3 = Vendor::firstOrCreate(
            ['name' => 'Metro Packaging & Office Supplies'],
            [
                'company_name' => 'Metro Supplies Inc',
                'phone' => '+1 (555) 456-7890',
                'email' => 'support@metrostationery.com',
                'address' => '102 Paper Mill Rd, Downtown',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Stationery and packaging supplies',
            ]
        );

        // 6. Customers
        $cust1 = Customer::firstOrCreate(
            ['name' => 'Robert Miller'],
            [
                'phone' => '+1 (555) 678-1122',
                'email' => 'robert.miller@email.com',
                'address' => '45 Oak Ridge Lane, Apt 3B',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Regular weekly neighborhood shopper',
            ]
        );

        $cust2 = Customer::firstOrCreate(
            ['name' => 'Elena Rostova (Green Cafe)'],
            [
                'phone' => '+1 (555) 789-2233',
                'email' => 'info@greencafebistro.com',
                'address' => '89 Riverside Promenade',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Local cafe buying juices and dairy in bulk',
            ]
        );

        $cust3 = Customer::firstOrCreate(
            ['name' => 'Walk-in Cash Customer'],
            [
                'phone' => null,
                'email' => null,
                'address' => 'Store Counter',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Standard walk-in customer for quick POS sales',
            ]
        );

        $cust4 = Customer::firstOrCreate(
            ['name' => 'David Kim'],
            [
                'phone' => '+1 (555) 890-3344',
                'email' => 'david.kim@techfirm.io',
                'address' => 'Suite 200, Innovation Tower',
                'opening_balance' => 0,
                'current_balance' => 0,
                'notes' => 'Corporate account for pantry and office snacks',
            ]
        );

        // 7. Seed Sample Vendor Bills (Purchases)
        if (VendorBill::count() === 0) {
            // Bill 1: Apex Wholesale
            $vBill1 = VendorBill::create([
                'bill_number' => 'PUR-20261101-0001',
                'vendor_reference_no' => 'INV-APX-8821',
                'vendor_id' => $vendor1->id,
                'bill_date' => Carbon::now()->subDays(10)->toDateString(),
                'due_date' => Carbon::now()->addDays(15)->toDateString(),
                'subtotal' => 280.00,
                'discount' => 10.00,
                'tax' => 0.00,
                'grand_total' => 270.00,
                'paid_amount' => 200.00,
                'due_amount' => 70.00,
                'payment_status' => 'partial',
                'payment_method' => 'bank_transfer',
                'notes' => 'Restock for November beverage items',
                'user_id' => $admin->id,
            ]);

            VendorBillItem::create([
                'vendor_bill_id' => $vBill1->id,
                'item_id' => $items['BEV-001']->id,
                'item_name' => $items['BEV-001']->name,
                'quantity' => 50,
                'unit_price' => 2.20,
                'total_price' => 110.00,
            ]);

            VendorBillItem::create([
                'vendor_bill_id' => $vBill1->id,
                'item_id' => $items['BEV-002']->id,
                'item_name' => $items['BEV-002']->name,
                'quantity' => 100,
                'unit_price' => 0.60,
                'total_price' => 60.00,
            ]);

            VendorBillItem::create([
                'vendor_bill_id' => $vBill1->id,
                'item_id' => $items['SNK-001']->id,
                'item_name' => $items['SNK-001']->name,
                'quantity' => 60,
                'unit_price' => 1.80,
                'total_price' => 110.00,
            ]);

            VendorPayment::create([
                'vendor_id' => $vendor1->id,
                'vendor_bill_id' => $vBill1->id,
                'payment_date' => Carbon::now()->subDays(9)->toDateString(),
                'amount' => 200.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TXN-BNK-9901',
                'notes' => 'Part payment via wire transfer',
                'user_id' => $admin->id,
            ]);

            $vendor1->recalculateBalance();

            // Bill 2: Global Agro (Paid)
            $vBill2 = VendorBill::create([
                'bill_number' => 'PUR-20261105-0002',
                'vendor_reference_no' => 'AGRO-4421',
                'vendor_id' => $vendor2->id,
                'bill_date' => Carbon::now()->subDays(6)->toDateString(),
                'due_date' => Carbon::now()->addDays(20)->toDateString(),
                'subtotal' => 340.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 340.00,
                'paid_amount' => 340.00,
                'due_amount' => 0.00,
                'payment_status' => 'paid',
                'payment_method' => 'cheque',
                'notes' => 'Rice and oil bulk delivery',
                'user_id' => $admin->id,
            ]);

            VendorBillItem::create([
                'vendor_bill_id' => $vBill2->id,
                'item_id' => $items['GRO-001']->id,
                'item_name' => $items['GRO-001']->name,
                'quantity' => 40,
                'unit_price' => 8.50,
                'total_price' => 340.00,
            ]);

            VendorPayment::create([
                'vendor_id' => $vendor2->id,
                'vendor_bill_id' => $vBill2->id,
                'payment_date' => Carbon::now()->subDays(5)->toDateString(),
                'amount' => 340.00,
                'payment_method' => 'cheque',
                'reference_number' => 'CHQ-88120',
                'notes' => 'Paid in full on receipt',
                'user_id' => $admin->id,
            ]);

            $vendor2->recalculateBalance();
        }

        // 8. Seed Sample Customer Bills (Sales)
        if (CustomerBill::count() === 0) {
            // Bill 1: Elena (Green Cafe) - Partial
            $cBill1 = CustomerBill::create([
                'bill_number' => 'INV-20261108-0001',
                'customer_id' => $cust2->id,
                'customer_name' => $cust2->name,
                'bill_date' => Carbon::now()->subDays(4)->toDateString(),
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'subtotal' => 97.00,
                'discount' => 5.00,
                'tax' => 0.00,
                'grand_total' => 92.00,
                'paid_amount' => 50.00,
                'due_amount' => 42.00,
                'payment_status' => 'partial',
                'payment_method' => 'cash',
                'notes' => 'Cafe weekly supply order',
                'user_id' => $admin->id,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill1->id,
                'item_id' => $items['BEV-001']->id,
                'item_name' => $items['BEV-001']->name,
                'quantity' => 15,
                'unit_price' => 3.50,
                'unit_cost' => 2.20,
                'total_price' => 52.50,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill1->id,
                'item_id' => $items['BEV-002']->id,
                'item_name' => $items['BEV-002']->name,
                'quantity' => 20,
                'unit_price' => 1.20,
                'unit_cost' => 0.60,
                'total_price' => 24.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill1->id,
                'item_id' => $items['SNK-002']->id,
                'item_name' => $items['SNK-002']->name,
                'quantity' => 4,
                'unit_price' => 5.50,
                'unit_cost' => 3.50,
                'total_price' => 22.00,
            ]);

            CustomerPayment::create([
                'customer_id' => $cust2->id,
                'customer_bill_id' => $cBill1->id,
                'payment_date' => Carbon::now()->subDays(4)->toDateString(),
                'amount' => 50.00,
                'payment_method' => 'cash',
                'reference_number' => 'CSH-001',
                'notes' => 'Initial deposit paid in cash',
                'user_id' => $admin->id,
            ]);

            $cust2->recalculateBalance();

            // Bill 2: Walk-in POS Sale (Fully Paid)
            $cBill2 = CustomerBill::create([
                'bill_number' => 'INV-20261110-0002',
                'customer_id' => $cust3->id,
                'customer_name' => 'Walk-in Customer',
                'bill_date' => Carbon::now()->subDays(2)->toDateString(),
                'due_date' => Carbon::now()->subDays(2)->toDateString(),
                'subtotal' => 24.50,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 24.50,
                'paid_amount' => 24.50,
                'due_amount' => 0.00,
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'notes' => 'Walk-in retail POS checkout',
                'user_id' => $admin->id,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill2->id,
                'item_id' => $items['GRO-001']->id,
                'item_name' => $items['GRO-001']->name,
                'quantity' => 1,
                'unit_price' => 12.00,
                'unit_cost' => 8.50,
                'total_price' => 12.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill2->id,
                'item_id' => $items['SNK-001']->id,
                'item_name' => $items['SNK-001']->name,
                'quantity' => 2,
                'unit_price' => 3.00,
                'unit_cost' => 1.80,
                'total_price' => 6.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill2->id,
                'item_id' => $items['GRO-002']->id,
                'item_name' => $items['GRO-002']->name,
                'quantity' => 1,
                'unit_price' => 6.50,
                'unit_cost' => 4.80,
                'total_price' => 6.50,
            ]);

            CustomerPayment::create([
                'customer_id' => $cust3->id,
                'customer_bill_id' => $cBill2->id,
                'payment_date' => Carbon::now()->subDays(2)->toDateString(),
                'amount' => 24.50,
                'payment_method' => 'card',
                'reference_number' => 'POS-CARD-9912',
                'notes' => 'Paid in full via Visa debit card',
                'user_id' => $admin->id,
            ]);

            $cust3->recalculateBalance();

            // Bill 3: David Kim (Today's Sale - Fully Paid)
            $cBill3 = CustomerBill::create([
                'bill_number' => 'INV-' . date('Ymd') . '-0003',
                'customer_id' => $cust4->id,
                'customer_name' => $cust4->name,
                'bill_date' => Carbon::today()->toDateString(),
                'due_date' => Carbon::today()->addDays(14)->toDateString(),
                'subtotal' => 63.00,
                'discount' => 3.00,
                'tax' => 0.00,
                'grand_total' => 60.00,
                'paid_amount' => 60.00,
                'due_amount' => 0.00,
                'payment_status' => 'paid',
                'payment_method' => 'mobile_banking',
                'notes' => 'Tech office snack restocking',
                'user_id' => $admin->id,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill3->id,
                'item_id' => $items['BEV-003']->id,
                'item_name' => $items['BEV-003']->name,
                'quantity' => 4,
                'unit_price' => 7.00,
                'unit_cost' => 4.50,
                'total_price' => 28.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill3->id,
                'item_id' => $items['SNK-001']->id,
                'item_name' => $items['SNK-001']->name,
                'quantity' => 7,
                'unit_price' => 3.00,
                'unit_cost' => 1.80,
                'total_price' => 21.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill3->id,
                'item_id' => $items['SNK-002']->id,
                'item_name' => $items['SNK-002']->name,
                'quantity' => 2,
                'unit_price' => 5.50,
                'unit_cost' => 3.50,
                'total_price' => 11.00,
            ]);

            CustomerBillItem::create([
                'customer_bill_id' => $cBill3->id,
                'item_id' => $items['BEV-002']->id,
                'item_name' => $items['BEV-002']->name,
                'quantity' => 2,
                'unit_price' => 1.50,
                'unit_cost' => 0.60,
                'total_price' => 3.00,
            ]);

            CustomerPayment::create([
                'customer_id' => $cust4->id,
                'customer_bill_id' => $cBill3->id,
                'payment_date' => Carbon::today()->toDateString(),
                'amount' => 60.00,
                'payment_method' => 'mobile_banking',
                'reference_number' => 'MBL-839210',
                'notes' => 'Paid via mobile banking',
                'user_id' => $admin->id,
            ]);

            $cust4->recalculateBalance();
        }
    }
}
