<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientController extends Controller
{
    /**
     * Daftar semua client.
     */
    public function index()
    {
        $clients = Client::with('user')->latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    /**
     * Form tambah client baru.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Simpan client baru + buat akun user (role=client) secara atomik.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email', 'unique:clients,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'company'  => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($validated) {
            // 1) Buat akun login untuk client
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'client',
            ]);

            // 2) Buat data client dan link ke user
            Client::create([
                'user_id' => $user->id,
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'phone'   => $validated['phone'] ?? null,
                'company' => $validated['company'] ?? null,
            ]);
        });

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil ditambahkan beserta akun login.');
    }

    /**
     * Detail client + daftar project miliknya.
     */
    public function show(Client $client)
    {
        $client->load(['user', 'projects']);
        return view('clients.show', compact('client'));
    }

    /**
     * Form edit data client.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update data client (dan user terkait).
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => [
                'required', 'email', 'max:255',
                // Abaikan record client yang sedang diedit (berdasarkan primary key)
                Rule::unique('clients', 'email')->ignore($client->id),
                // Abaikan user yang terhubung ke client ini (berdasarkan user_id)
                Rule::unique('users', 'email')->ignore($client->user_id),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'company'  => ['nullable', 'string', 'max:255'],
            // 'sometimes' → validasi hanya berjalan jika field dikirim DAN tidak kosong
            // 'nullable'  → izinkan nilai null/kosong (skip seluruh rule jika null)
            // 'confirmed' → cocokkan dengan password_confirmation (hanya jika diisi)
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated, $client, $request) {
            // Update tabel clients — TIDAK membuat record baru
            $client->update([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'phone'   => $validated['phone'] ?? null,
                'company' => $validated['company'] ?? null,
            ]);

            // Update akun login user — TIDAK membuat user baru
            if ($client->user) {
                $userData = [
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                    'role'  => 'client',
                ];
                // Password hanya diupdate jika benar-benar diisi (tidak kosong)
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $client->user->update($userData);
            }
        });

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil diperbarui.');
    }

    /**
     * Hapus client dan akun user terkait secara atomik.
     */
    public function destroy(Client $client)
    {
        DB::transaction(function () use ($client) {
            $user = $client->user;
            $client->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }
}
