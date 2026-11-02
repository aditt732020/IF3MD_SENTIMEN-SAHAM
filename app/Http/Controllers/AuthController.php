<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // =============================================
    // LOGIN
    // =============================================

    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses login - FR-01 (Admin login dengan username atau email)
     */
    public function login(Request $request)
    {
        // NFR-04: Validasi & sanitasi input untuk mencegah SQL Injection
        $request->validate([
            'email'    => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
        ], [
            'email.required'    => 'Email / Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
        ]);

        $loginInput = trim($request->input('email'));
        $password   = $request->input('password');
        $remember   = $request->boolean('remember');

        // Bisa login menggunakan email maupun username (kolom name)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$field => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            // FR-02: Cek role, arahkan ke dashboard
            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Username/Email atau kata sandi tidak valid.']);
    }

    // =============================================
    // REGISTER
    // =============================================

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Proses register admin baru
     */
    public function register(Request $request)
    {
        // NFR-04: Validasi & sanitasi input
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama maksimal 100 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Daftarkan sebagai visitor secara default (FR-02)
        // Admin hanya bisa diset melalui seeder atau oleh admin lain
        User::create([
            'name'     => strip_tags($request->name),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'visitor',
        ]);

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // =============================================
    // LOGOUT
    // =============================================

    /**
     * Proses logout - FR-01
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}
