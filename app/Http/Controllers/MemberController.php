<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $members = DB::table('members')->when($search, fn ($q) => $q->where(fn ($query) => $query->where('full_name', 'like', "%{$search}%")->orWhere('member_no', 'like', "%{$search}%")))->orderByDesc('id')->paginate(10)->withQueryString();

        return view('members.index', compact('members', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['full_name' => ['required', 'string', 'max:255'], 'ic_no' => ['required', 'string', 'max:30', 'unique:members,ic_no'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string'], 'joined_at' => ['required', 'date']]);
        $data['member_no'] = 'A-'.str_pad((string) ((int) DB::table('members')->max('id') + 1001), 4, '0', STR_PAD_LEFT);
        $data['status'] = 'active';
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $memberId = DB::table('members')->insertGetId($data);
        DB::table('savings_accounts')->insert(['member_id' => $memberId, 'account_no' => 'SAV-'.str_pad((string) $memberId, 4, '0', STR_PAD_LEFT), 'account_type' => 'monthly', 'balance' => 0, 'status' => 'active', 'opened_at' => $data['joined_at'], 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Ahli baharu berjaya didaftarkan.');
    }

    public function update(Request $request, int $member): RedirectResponse
    {
        $data = $request->validate(['full_name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string'], 'status' => ['required', 'in:active,inactive,suspended']]);
        DB::table('members')->where('id', $member)->update([...$data, 'updated_at' => now()]);

        return back()->with('success', 'Maklumat ahli dikemaskini.');
    }
}
