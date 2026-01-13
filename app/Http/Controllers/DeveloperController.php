<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
                'unique:developers,email,' . $developer->id,
                'unique:users,email,' . ($developer->user_id ?? 'NULL'),
            ],

            'role'  => ['required', 'in:frontend,backend,fullstack,pm'],
            'skill' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // update tabel developers
        $developer->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'skill' => $validated['skill'] ?? '-',
        ]);

        // update tabel users (akun login)
        if ($developer->user) {
            $userData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'role'  => 'developer',
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
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
