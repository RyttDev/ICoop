<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SavingsController extends Controller
{
    public function index(): View
    {
        return view('savings.index', ['accounts' => DB::table('savings_accounts')->join('members', 'members.id', '=', 'savings_accounts.member_id')->select('savings_accounts.*', 'members.full_name', 'members.member_no')->orderByDesc('savings_accounts.id')->get(), 'transactions' => DB::table('savings_transactions')->join('savings_accounts', 'savings_accounts.id', '=', 'savings_transactions.savings_account_id')->join('members', 'members.id', '=', 'savings_accounts.member_id')->select('savings_transactions.*', 'members.full_name')->latest('transaction_date')->limit(12)->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['savings_account_id' => ['required', 'exists:savings_accounts,id'], 'transaction_type' => ['required', 'in:deposit,withdrawal'], 'amount' => ['required', 'numeric', 'min:0.01'], 'transaction_date' => ['required', 'date'], 'notes' => ['nullable', 'string', 'max:500']]);
        DB::transaction(function () use ($data) {
            $account = DB::table('savings_accounts')->lockForUpdate()->find($data['savings_account_id']);
            if ($data['transaction_type'] === 'withdrawal' && $account->balance < $data['amount']) {
                throw ValidationException::withMessages(['amount' => 'Baki simpanan tidak mencukupi.']);
            }
            $newBalance = $data['transaction_type'] === 'deposit' ? $account->balance + $data['amount'] : $account->balance - $data['amount'];
            DB::table('savings_accounts')->where('id', $account->id)->update(['balance' => $newBalance, 'updated_at' => now()]);
            DB::table('savings_transactions')->insert([...$data, 'reference_no' => 'SAV-TX-'.now()->format('YmdHis').'-'.$account->id, 'recorded_by' => DB::table('users')->value('id'), 'created_at' => now(), 'updated_at' => now()]);
        });

        return back()->with('success', 'Transaksi simpanan berjaya direkodkan.');
    }
}
