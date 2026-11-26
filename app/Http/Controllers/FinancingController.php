<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancingController extends Controller
{
    public function index(): View
    {
        return view('financing.index', ['applications' => DB::table('financing_applications')->join('members', 'members.id', '=', 'financing_applications.member_id')->join('financing_products', 'financing_products.id', '=', 'financing_applications.financing_product_id')->select('financing_applications.*', 'members.full_name', 'financing_products.name as product_name')->latest('applied_at')->get(), 'members' => DB::table('members')->where('status', 'active')->orderBy('full_name')->get(), 'products' => DB::table('financing_products')->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['member_id' => ['required', 'exists:members,id'], 'financing_product_id' => ['required', 'exists:financing_products,id'], 'requested_amount' => ['required', 'numeric', 'min:1'], 'tenure_months' => ['required', 'integer', 'min:1'], 'purpose' => ['nullable', 'string']]);
        DB::table('financing_applications')->insert([...$data, 'application_no' => 'FIN-'.now()->format('Y').'-'.str_pad((string) ((int) DB::table('financing_applications')->max('id') + 1), 4, '0', STR_PAD_LEFT), 'applied_at' => today(), 'status' => 'submitted', 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Permohonan pembiayaan berjaya direkodkan.');
    }

    public function updateStatus(Request $request, int $application): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:review,pending_document,approved,rejected,disbursed,completed'], 'approved_amount' => ['nullable', 'numeric', 'min:0']]);
        DB::table('financing_applications')->where('id', $application)->update([...$data, 'approved_at' => in_array($data['status'], ['approved', 'disbursed']) ? today() : null, 'reviewed_by' => DB::table('users')->value('id'), 'updated_at' => now()]);

        return back()->with('success', 'Status pembiayaan berjaya dikemaskini.');
    }
}
