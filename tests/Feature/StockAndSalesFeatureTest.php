<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockAndSalesFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_stock_can_be_added_and_updated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->post(route('stock.store'), [
            'sku' => 'SKU-001',
            'name' => 'Produk Ujian',
            'category' => 'Barang Keperluan',
            'description' => 'Stok ujian',
            'quantity' => 10,
            'reorder_level' => 3,
            'cost_price' => 5.50,
            'selling_price' => 8.00,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock_items', ['sku' => 'SKU-001', 'quantity' => 10]);

        $item = DB::table('stock_items')->where('sku', 'SKU-001')->first();

        $this->put(route('stock.update', $item->id), [
            'sku' => 'SKU-001',
            'name' => 'Produk Dikemaskini',
            'category' => 'Barang Keperluan',
            'description' => null,
            'quantity' => 25,
            'reorder_level' => 5,
            'cost_price' => 6.00,
            'selling_price' => 9.00,
        ])->assertRedirect();

        $this->assertDatabaseHas('stock_items', ['id' => $item->id, 'name' => 'Produk Dikemaskini', 'quantity' => 25]);
    }

    public function test_sales_statistics_use_the_selected_year_and_ignore_drafts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $memberId = DB::table('members')->insertGetId([
            'member_no' => 'M-001',
            'full_name' => 'Ahli Ujian',
            'ic_no' => '900101-01-0001',
            'joined_at' => '2025-01-01',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('invoices')->insert([
            [
                'invoice_no' => 'INV-2025-0001',
                'document_type' => 'invoice',
                'member_id' => $memberId,
                'issue_date' => '2025-03-15',
                'total' => 1200,
                'status' => 'issued',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'invoice_no' => 'INV-2025-0002',
                'document_type' => 'invoice',
                'member_id' => $memberId,
                'issue_date' => '2025-03-20',
                'total' => 800,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->get(route('sales.index', ['year' => 2025]));

        $response->assertOk()
            ->assertSee('Statistik Jualan Bulanan')
            ->assertSee('RM 1,200.00')
            ->assertSee('Mac');
    }
}
