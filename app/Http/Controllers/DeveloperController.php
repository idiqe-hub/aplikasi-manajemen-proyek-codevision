<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DeveloperController extends Controller
{
    public function index()
    {
        $developers = Developer::latest()->paginate(10);
        return view('developers.index', compact('developers'));
    }

    public function create()
    {
        return view('developers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email', 'unique:developers,email'],
            'role'     => ['required', 'in:frontend,backend,fullstack,pm'],
            'skill'    => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 1) buat user login developer
        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'developer',
        ]);

        // 2) buat data developer + link ke user
        Developer::create([
            'user_id' => $user->id,
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role'    => $validated['role'],
            'skill'   => $validated['skill'] ?? '-',
        ]);

        return redirect()->route('developers.index')
            ->with('success', 'Developer berhasil ditambahkan + akun login dibuat.');
    }

    public function show(Developer $developer)
    {
        return view('developers.show', compact('developer'));
    }

    public function edit(Developer $developer)
    {
        return view('developers.edit', compact('developer'));
    }

    public function update(Request $request, Developer $developer)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                // Abaikan record developer yang sedang diedit (berdasarkan primary key)
                Rule::unique('developers', 'email')->ignore($developer->id),
                // Abaikan user yang terhubung ke developer ini (berdasarkan user_id)
                Rule::unique('users', 'email')->ignore($developer->user_id),
            ],
            'role'  => ['required', 'in:frontend,backend,fullstack,pm'],
            'skill' => ['nullable', 'string', 'max:255'],
            'password' => ['sometimes', 'nullable', 'confirmed', 'min:8'],
        ]);

        // Update tabel developers — TIDAK membuat record baru
        $developer->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'skill' => $validated['skill'] ?? '-',
        ]);

        // Update tabel users (akun login) — TIDAK membuat user baru
        if ($developer->user) {
            $userData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'role'  => 'developer',
            ];

            // Password hanya diupdate jika benar-benar diisi (tidak kosong)
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $developer->user->update($userData);
        }

        return redirect()->route('developers.index')
            ->with('success', 'Developer berhasil diupdate.');
    }


    public function destroy(Developer $developer)
    {
        DB::transaction(function () use ($developer) {
            // simpan user 
            $user = $developer->user;

            // hapus developer
            $developer->delete();

            // hapus akun login
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('developers.index')
            ->with('success', 'Developer berhasil dihapus.');
    }

    public function resetPassword(Request $request, Developer $developer)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!$developer->user) {
            return back()->with('error', 'Akun user untuk developer ini tidak ditemukan.');
        }

        $developer->user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password developer berhasil direset.');
    }
}
