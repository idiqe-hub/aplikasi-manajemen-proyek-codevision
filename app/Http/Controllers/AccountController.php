<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    public function editEmail()
    {
        // Hanya admin yang boleh mengubah email
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $user = auth()->user();
        return view('account.email', compact('user'));
    }

    public function updateEmail(Request $request)
    {
        // Hanya admin yang boleh mengubah email
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $user = auth()->user();

        $request->validate([
            'email'            => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => ['required'],
        ]);

        // Verifikasi password sebelum mengubah email
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password tidak sesuai.',
            ])->withInput();
        }

        $user->update(['email' => $request->email]);

        return back()->with('success', 'Email berhasil diubah menjadi ' . $request->email . '.');
    }

    public function editPassword()
    {
        return view('account.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // opsional: biar aman bisa logout paksa
        // auth()->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login ulang.');

        return back()->with('success', 'Password berhasil diubah.');
    }
}
