<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('cari')) {
            $query->where('name', 'like', '%' . $request->cari . '%')
                ->orWhere('email', 'like', '%' . $request->cari . '%');
        }
        $users = $query->orderBy('id')->paginate(15)->withQueryString();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'apoteker', 'kasir'])],
        ]);

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'aktif' => true,
        ]);

        \App\Models\ActivityLog::log(
            'Tambah User',
            "User: {$newUser->name} ({$newUser->email}), Role: " . ucfirst($newUser->role),
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User berhasil ditambahkan.'], 201);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'apoteker', 'kasir'])],
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        \App\Models\ActivityLog::log(
            'Update User',
            "User: {$user->name} ({$user->email}), Role: " . ucfirst($user->role),
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User berhasil diperbarui.']);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun yang sedang kamu gunakan.');
        }

        $user->update(['aktif' => ! $user->aktif]);

        $status = $user->aktif ? 'diaktifkan kembali' : 'dinonaktifkan';
        \App\Models\ActivityLog::log(
            'Toggle User',
            "User: {$user->name} {$status}",
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        if ($user->penerimaan()->exists() || $user->penjualan()->exists()) {
            return back()->with('error', 'User punya riwayat transaksi, tidak bisa dihapus. Nonaktifkan saja.');
        }

        \App\Models\ActivityLog::log(
            'Hapus User',
            "User: {$user->name} ({$user->email})",
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}