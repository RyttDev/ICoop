<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::query()->orderBy('role')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:staff,admin'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $user = User::create($data);

        return back()->with('success', "Akaun {$user->name} berjaya dicipta.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:staff,admin'],
            'password' => ['nullable', 'confirmed', 'string', 'min:8'],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak boleh menukar peranan akaun sendiri daripada Admin.']);
        }

        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() < 2) {
            return back()->withErrors(['role' => 'Sistem mesti mempunyai sekurang-kurangnya seorang Admin.']);
        }

        if (blank($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return back()->with('success', "Akaun {$user->name} berjaya dikemaskini.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Anda tidak boleh memadam akaun sendiri.']);
        }
        if ($user->role === 'admin' && User::where('role', 'admin')->count() < 2) {
            return back()->withErrors(['user' => 'Admin terakhir tidak boleh dipadam.']);
        }
        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akaun {$name} berjaya dipadam.");
    }
}
