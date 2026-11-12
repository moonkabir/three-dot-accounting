<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerBill;
use App\Models\Item;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'email' => 'admin@threedot.com',
            'name' => 'Shop Admin',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_root_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign in to your account');
        $response->assertSee('admin@threedot.com');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@threedot.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_authenticated_user_accesses_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Customer Due');
        $response->assertSee('Vendor Due');
        $response->assertSee('New Sale Bill');
    }

    public function test_inventory_index_and_creation(): void
    {
        $response = $this->actingAs($this->user)->get('/inventory');
        $response->assertStatus(200);

        // Test creating an item
        $sku = 'TEST-' . time();
        $createResponse = $this->actingAs($this->user)->post('/inventory', [
            'name' => 'Test Energy Drink',
            'sku' => $sku,
            'unit' => 'can',
            'cost_price' => 1.50,
            'selling_price' => 2.50,
            'stock_quantity' => 20,
            'alert_quantity' => 5,
        ]);

        $createResponse->assertRedirect('/inventory');
        $this->assertDatabaseHas('items', ['sku' => $sku]);
    }

    public function test_customer_management_and_sales_billing(): void
    {
        $response = $this->actingAs($this->user)->get('/customers');
        $response->assertStatus(200);

        // Create customer
        $cust = Customer::create([
            'name' => 'Test Customer ' . time(),
            'phone' => '1234567890',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $item = Item::create([
            'name' => 'Sample Juice',
            'sku' => 'SKU-' . time(),
            'unit' => 'bottle',
            'cost_price' => 2.00,
            'selling_price' => 3.50,
            'stock_quantity' => 20,
            'alert_quantity' => 5,
            'status' => 'active',
        ]);
        $initialStock = (float) $item->stock_quantity;

        // Create customer bill
        $billNumber = 'TEST-INV-' . time();
        $billResponse = $this->actingAs($this->user)->post('/customer-bills', [
            'bill_number' => $billNumber,
            'customer_id' => $cust->id,
            'bill_date' => date('Y-m-d'),
            'discount' => 0,
            'tax' => 0,
            'paid_amount' => 1.00,
            'payment_method' => 'cash',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                    'unit_price' => $item->selling_price,
                ],
            ],
        ]);

        $billResponse->assertRedirect();
        $this->assertDatabaseHas('customer_bills', ['bill_number' => $billNumber]);

        // Check stock auto-decrement
        $item->refresh();
        $this->assertEquals($initialStock - 2, (float) $item->stock_quantity);
    }

    public function test_vendor_management_and_purchase_billing(): void
    {
        $response = $this->actingAs($this->user)->get('/vendors');
        $response->assertStatus(200);

        $vendor = Vendor::create([
            'name' => 'Test Vendor ' . time(),
            'company_name' => 'Test Vendor Co',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $item = Item::create([
            'name' => 'Restock Biscuits',
            'sku' => 'BIS-' . time(),
            'unit' => 'pack',
            'cost_price' => 1.00,
            'selling_price' => 1.80,
            'stock_quantity' => 10,
            'alert_quantity' => 5,
            'status' => 'active',
        ]);
        $initialStock = (float) $item->stock_quantity;

        // Create purchase bill
        $purNumber = 'TEST-PUR-' . time();
        $purResponse = $this->actingAs($this->user)->post('/vendor-bills', [
            'bill_number' => $purNumber,
            'vendor_id' => $vendor->id,
            'bill_date' => date('Y-m-d'),
            'discount' => 0,
            'tax' => 0,
            'paid_amount' => 10.00,
            'payment_method' => 'cash',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 5,
                    'unit_price' => 2.00,
                ],
            ],
        ]);

        $purResponse->assertRedirect();
        $this->assertDatabaseHas('vendor_bills', ['bill_number' => $purNumber]);

        // Check stock auto-increment
        $item->refresh();
        $this->assertEquals($initialStock + 5, (float) $item->stock_quantity);
    }

    public function test_financial_reports_and_cash_book(): void
    {
        $repResponse = $this->actingAs($this->user)->get('/reports');
        $repResponse->assertStatus(200);
        $repResponse->assertSee('Gross Profit');

        $cashResponse = $this->actingAs($this->user)->get('/reports/cash-book');
        $cashResponse->assertStatus(200);
        $cashResponse->assertSee('Cash & Bank Journal');
    }
}
