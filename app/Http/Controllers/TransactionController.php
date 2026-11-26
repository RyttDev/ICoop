<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        return view('transactions.index', ['transactions' => DB::table('financial_transactions')->leftJoin('members', 'members.id', '=', 'financial_transactions.member_id')->select('financial_transactions.*', 'members.full_name')->latest('transaction_date')->paginate(15), 'members' => DB::table('members')->where('status', 'active')->orderBy('full_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['transaction_type' => ['required', 'in:income,expense'], 'category' => ['required', 'string', 'max:120'], 'description' => ['required', 'string', 'max:500'], 'amount' => ['required', 'numeric', 'min:0.01'], 'transaction_date' => ['required', 'date'], 'payment_method' => ['required', 'in:cash,bank_transfer,online,cheque'], 'member_id' => ['nullable', 'exists:members,id']]);
        DB::table('financial_transactions')->insert([...$data, 'transaction_no' => 'TRX-'.now()->format('YmdHis'), 'recorded_by' => DB::table('users')->value('id'), 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Transaksi berjaya direkodkan.');
    }
}
