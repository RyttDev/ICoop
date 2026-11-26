<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesStatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
        ]);
        $year = (int) $request->input('year', now()->year);
        $invoices = DB::table('invoices')
            ->select(['issue_date', 'total'])
            ->where('document_type', 'invoice')
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->get();
        $approvedReports = DB::table('daily_sales_reports')
            ->selectRaw('sales_date as issue_date, (cash_sales + online_sales) as total')
            ->where('status', 'approved')
            ->get();
        $salesRecords = $invoices->concat($approvedReports);

        $availableYears = $salesRecords
            ->map(fn (object $invoice): int => Carbon::parse($invoice->issue_date)->year)
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $monthlySales = $salesRecords
            ->filter(fn (object $invoice): bool => Carbon::parse($invoice->issue_date)->year === $year)
            ->groupBy(fn (object $invoice): int => Carbon::parse($invoice->issue_date)->month)
            ->map(fn ($month): float => (float) $month->sum('total'));

        $monthNames = ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
        $months = collect(range(1, 12))->map(fn (int $month): array => [
            'name' => $monthNames[$month - 1],
            'total' => $monthlySales->get($month, 0),
        ]);

        return view('sales.index', [
            'year' => $year,
            'availableYears' => $availableYears,
            'months' => $months,
            'annualTotal' => $months->sum('total'),
        ]);
    }
}
