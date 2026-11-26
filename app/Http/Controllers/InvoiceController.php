<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('invoices.index', ['invoices' => DB::table('invoices')->join('members', 'members.id', '=', 'invoices.member_id')->select('invoices.*', 'members.full_name')->latest('issue_date')->get(), 'members' => DB::table('members')->where('status', 'active')->orderBy('full_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['document_type' => ['required', 'in:invoice,quotation'], 'member_id' => ['required', 'exists:members,id'], 'attention_to' => ['nullable', 'string', 'max:255'], 'issue_date' => ['required', 'date'], 'due_date' => ['nullable', 'date'], 'quotation_expires_at' => ['nullable', 'date'], 'discount' => ['nullable', 'numeric', 'min:0'], 'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'], 'shipping_fee' => ['nullable', 'numeric', 'min:0'], 'items' => ['required', 'array', 'min:1'], 'items.*.description' => ['required', 'string', 'max:255'], 'items.*.quantity' => ['required', 'numeric', 'min:0.01'], 'items.*.unit_price' => ['required', 'numeric', 'min:0.01'], 'notes' => ['nullable', 'string']]);
        $subtotal = collect($data['items'])->sum(fn ($item) => (float) $item['quantity'] * (float) $item['unit_price']);
        $discount = (float) ($data['discount'] ?? 0); $taxRate = (float) ($data['tax_rate'] ?? 0); $shipping = (float) ($data['shipping_fee'] ?? 0); $taxAmount = max(0, $subtotal - $discount) * ($taxRate / 100); $total = max(0, $subtotal - $discount) + $taxAmount + $shipping;
        $id = DB::transaction(function () use ($data, $subtotal, $discount, $taxRate, $taxAmount, $shipping, $total, $request) {
            $prefix = $data['document_type'] === 'quotation' ? 'QT' : 'INV';
            $id = DB::table('invoices')->insertGetId(['invoice_no' => $prefix.'-'.now()->format('Y').'-'.str_pad((string) ((int) DB::table('invoices')->max('id') + 1), 4, '0', STR_PAD_LEFT), 'document_type' => $data['document_type'], 'member_id' => $data['member_id'], 'attention_to' => $data['attention_to'], 'issue_date' => $data['issue_date'], 'due_date' => $data['due_date'], 'quotation_expires_at' => $data['quotation_expires_at'] ?? null, 'subtotal' => $subtotal, 'discount' => $discount, 'tax_rate' => $taxRate, 'tax_amount' => $taxAmount, 'shipping_fee' => $shipping, 'total' => $total, 'status' => $data['document_type'] === 'quotation' ? 'draft' : 'issued', 'notes' => $data['notes'], 'created_by' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()]);
            foreach ($data['items'] as $item) {
                $amount = $item['quantity'] * $item['unit_price'];
                DB::table('invoice_items')->insert(['invoice_id' => $id, 'description' => $item['description'], 'quantity' => $item['quantity'], 'unit_price' => $item['unit_price'], 'amount' => $amount, 'created_at' => now(), 'updated_at' => now()]);
            }

            DB::table('activity_logs')->insert(['user_id' => $request->user()->id, 'action' => 'Cipta '.ucfirst($data['document_type']), 'subject_type' => 'invoice', 'subject_id' => $id, 'details' => $prefix.' #'.$id, 'created_at' => now(), 'updated_at' => now()]);

            return $id;
        });

        return redirect()->route('invoices.index')->with('success', ucfirst($data['document_type'])." #{$id} berjaya dicipta.");
    }

    public function updateStatus(Request $request, int $invoice): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:draft,issued,paid,overdue,cancelled,accepted,rejected'], 'paid_at' => ['nullable', 'date']]);
        $document = DB::table('invoices')->find($invoice); abort_unless($document, 404);
        DB::table('invoices')->where('id', $invoice)->update(['status' => $data['status'], 'paid_at' => $data['status'] === 'paid' ? ($data['paid_at'] ?? now()) : null, 'updated_at' => now()]);
        DB::table('activity_logs')->insert(['user_id' => $request->user()->id, 'action' => 'Kemaskini status dokumen', 'subject_type' => 'invoice', 'subject_id' => $invoice, 'details' => $data['status'], 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Status dokumen dikemaskini.');
    }

    public function convertQuotation(Request $request, int $invoice): RedirectResponse
    {
        $document = DB::table('invoices')->find($invoice); abort_unless($document && $document->document_type === 'quotation', 404);
        DB::table('invoices')->where('id', $invoice)->update(['document_type' => 'invoice', 'invoice_no' => 'INV-'.now()->format('Y').'-'.str_pad((string) $invoice, 4, '0', STR_PAD_LEFT), 'status' => 'issued', 'updated_at' => now()]);
        DB::table('activity_logs')->insert(['user_id' => $request->user()->id, 'action' => 'Tukar quotation kepada invois', 'subject_type' => 'invoice', 'subject_id' => $invoice, 'details' => null, 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Quotation berjaya ditukar kepada invois.');
    }

    public function pdf(int $invoice)
    {
        $data = $this->pdfData($invoice);

        return Pdf::loadView('invoices.pdf', $data)->setPaper('a4')->download($data['invoice']->invoice_no.'.pdf');
    }

    public function preview(int $invoice)
    {
        $data = $this->pdfData($invoice);
        return Pdf::loadView('invoices.pdf', $data)->setPaper('a4')->stream($data['invoice']->invoice_no.'.pdf');
    }

    private function pdfData(int $invoice): array
    {
        return ['invoice' => DB::table('invoices')->join('members', 'members.id', '=', 'invoices.member_id')->where('invoices.id', $invoice)->select('invoices.*', 'members.full_name', 'members.member_no', 'members.address', 'members.email', 'members.phone')->firstOrFail(), 'items' => DB::table('invoice_items')->where('invoice_id', $invoice)->get(), 'settings' => DB::table('cooperative_settings')->first()];
    }
}
