<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stockItems' => DB::table('stock_items')->count(),
            'lowStockItems' => DB::table('stock_items')->whereColumn('quantity', '<=', 'reorder_level')->count(),
            'activeMembers' => DB::table('members')->where('status', 'active')->count(),
            'monthlySales' => DB::table('invoices')->where('document_type', 'invoice')->whereNotIn('status', ['draft', 'cancelled'])->whereMonth('issue_date', now()->month)->whereYear('issue_date', now()->year)->sum('total') + DB::table('daily_sales_reports')->where('status', 'approved')->whereMonth('sales_date', now()->month)->whereYear('sales_date', now()->year)->sum(DB::raw('cash_sales + online_sales')),
            'activities' => DB::table('financial_transactions')->latest('transaction_date')->limit(6)->get(),
            'applications' => DB::table('financing_applications')->join('members', 'members.id', '=', 'financing_applications.member_id')->select('financing_applications.*', 'members.full_name')->latest('applied_at')->limit(5)->get(),
        ]);
    }
}
