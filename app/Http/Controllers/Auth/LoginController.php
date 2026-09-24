<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        $apotek = \Illuminate\Support\Facades\Cache::remember(
            'info_apotek',
            now()->addHours(6),
            fn () => \App\Models\InfoApotek::first()
        );

        return view('auth.login', compact('apotek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha' => ['required', 'captcha'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        if (! $user->aktif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun kamu tidak aktif. Hubungi admin.',
            ]);
        }

        $request->session()->regenerate();

        \App\Models\ActivityLog::log(
            'Login User',
            "User: {$user->name} ({$user->role}) berhasil login ke sistem",
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        $isAuto = $request->boolean('auto_logout');

        $user = Auth::user();
        if ($user) {
            \App\Models\ActivityLog::log(
                $isAuto ? 'Auto Logout User' : 'Logout User',
                "User: {$user->name} ({$user->role}) " . ($isAuto ? 'keluar otomatis karena tidak ada aktivitas' : 'keluar dari sistem'),
                \App\Models\ActivityLog::CATEGORY_KEAMANAN
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with(
            'notice',
            $isAuto
                ? 'Kamu tidak melakukan aktivitas selama 30 menit. Silakan login kembali.'
                : null
        );
    }
}
