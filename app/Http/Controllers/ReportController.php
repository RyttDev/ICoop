<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $isAdmin = $request->user()->role === 'admin';

        return view('reports.index', [
            'isAdmin' => $isAdmin,
            'reports' => DB::table('daily_sales_reports')
                ->join('users', 'users.id', '=', 'daily_sales_reports.user_id')
                ->when(! $isAdmin, fn ($query) => $query->where('daily_sales_reports.user_id', $request->user()->id))
                ->select('daily_sales_reports.*', 'users.name as staff_name')
                ->orderByDesc('sales_date')->orderByDesc('daily_sales_reports.id')->get(),
            'monthlyTotal' => DB::table('daily_sales_reports')->where('status', 'approved')->whereBetween('sales_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->sum(DB::raw('cash_sales + online_sales')),
            'pendingCount' => DB::table('daily_sales_reports')->where('status', 'pending')->count(),
            'approvedCount' => DB::table('daily_sales_reports')->where('status', 'approved')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sales_date' => ['required', 'date', 'before_or_equal:today'],
            'shift' => ['required', 'in:pagi,petang,penuh'],
            'cash_sales' => ['required', 'numeric', 'min:0'],
            'online_sales' => ['required', 'numeric', 'min:0'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        if ((float) $data['cash_sales'] + (float) $data['online_sales'] <= 0) {
            return back()->withErrors(['cash_sales' => 'Masukkan sekurang-kurangnya satu jumlah jualan.'])->withInput();
        }
        DB::table('daily_sales_reports')->updateOrInsert(
            ['user_id' => $request->user()->id, 'sales_date' => $data['sales_date'], 'shift' => $data['shift']],
            [...$data, 'status' => 'pending', 'admin_notes' => null, 'reviewed_by' => null, 'reviewed_at' => null, 'updated_at' => now(), 'created_at' => now()]
        );
        return back()->with('success', 'Laporan jualan telah dihantar kepada Admin.');
    }

    public function review(Request $request, int $report): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['status' => ['required', 'in:approved,rejected'], 'admin_notes' => ['nullable', 'string', 'max:1000']]);
        DB::table('daily_sales_reports')->where('id', $report)->update([...$data, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now(), 'updated_at' => now()]);
        return back()->with('success', $data['status'] === 'approved' ? 'Laporan telah disahkan.' : 'Laporan telah ditolak.');
    }
}
