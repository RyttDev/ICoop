<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        return view('stock.index', [
            'items' => DB::table('stock_items')->orderBy('name')->get(),
            'memo' => DB::table('inventory_memos')->find(1),
            'recentMovements' => DB::table('stock_movements')
                ->join('stock_items', 'stock_items.id', '=', 'stock_movements.stock_item_id')
                ->select('stock_movements.*', 'stock_items.name as item_name')
                ->latest('stock_movements.created_at')->limit(5)->get(),
        ]);
    }

    public function updateMemo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'assigned_staff' => ['nullable', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $memoValues = [...$data, 'updated_by' => $request->user()->id, 'updated_at' => now()];
        if (DB::table('inventory_memos')->where('id', 1)->exists()) {
            DB::table('inventory_memos')->where('id', 1)->update($memoValues);
        } else {
            DB::table('inventory_memos')->insert(['id' => 1, ...$memoValues, 'created_at' => now()]);
        }

        return back()->with('success', 'Memo tugasan inventori berjaya dikemaskini.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::table('stock_items')->insert([...$data, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Stok berjaya ditambah.');
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $data = $this->validatedData($request, $item);

        DB::table('stock_items')->where('id', $item)->update([...$data, 'updated_at' => now()]);

        return back()->with('success', 'Stok berjaya dikemaskini.');
    }

    public function movement(Request $request, int $item): RedirectResponse
    {
        $data = $request->validate([
            'movement_type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $item, $request): void {
            $stockItem = DB::table('stock_items')->lockForUpdate()->find($item);
            abort_unless($stockItem, 404);

            if ($data['movement_type'] === 'out' && $data['quantity'] > $stockItem->quantity) {
                abort(422, 'Kuantiti keluar melebihi baki stok semasa.');
            }

            $newQuantity = $data['movement_type'] === 'in'
                ? $stockItem->quantity + $data['quantity']
                : $stockItem->quantity - $data['quantity'];

            DB::table('stock_items')->where('id', $item)->update(['quantity' => $newQuantity, 'updated_at' => now()]);
            DB::table('stock_movements')->insert([
                'stock_item_id' => $item,
                'user_id' => $request->user()->id,
                'movement_type' => $data['movement_type'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', $data['movement_type'] === 'in' ? 'Stok berjaya ditambah.' : 'Stok berjaya dikeluarkan.');
    }

    public function destroy(int $item): RedirectResponse
    {
        DB::table('stock_items')->where('id', $item)->delete();

        return back()->with('success', 'Barangan berjaya dihapuskan.');
    }

    private function validatedData(Request $request, ?int $item = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:stock_items,sku'.($item ? ','.$item : '')],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
