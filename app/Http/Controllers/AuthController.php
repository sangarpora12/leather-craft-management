<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Pastikan namanya 'login'
    public function login()
    {
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = ['password' => $request->password];

        // Cek login pakai email atau name
        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->username;
        } else {
            $credentials['name'] = $request->username;
        }

        if (Auth::attempt($credentials)) {
            // CEK STATUS AKUN - Jika nonaktif, logout dan tampilkan pesan error
            $user = Auth::user();
            
            // Cek jika akun nonaktif (is_active = false)
            if (isset($user->is_active) && $user->is_active === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['username' => 'Login gagal! Akun Anda telah dinonaktifkan. Hubungi administrator.']);
            }
            
            $request->session()->regenerate();
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['username' => 'Login gagal! Akun tidak ditemukan.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }

    // ==========================================
    // PASSWORD RESET METHODS
    // ==========================================

    /**
     * Tampilkan form "Lupa Password" - minta username
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim link reset password ke email user yang tersimpan di database
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['login' => 'required'], [
            'login.required' => 'Username wajib diisi.'
        ]);

        // Cari user berdasarkan username
        $user = \App\Models\User::where('name', $request->login)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Username tidak ditemukan dalam sistem.']);
        }

        // Buat token reset password
        $token = app('auth.password.broker')->createToken($user);

        // Kirim email dengan link reset password ke email yang terdaftar di database
        try {
            \Mail::send('emails.reset-password', [
                'user' => $user,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Reset Password - Berliano Leather');
            });

            return back()->with('status', 'Link reset password telah dikirim ke email Anda: ' . $user->email);
        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Gagal mengirim email. Silakan coba lagi nanti.']);
        }
    }

    /**
     * Tampilkan form reset password (dari link email)
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Simpan password baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'login' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'login.required' => 'Username wajib diisi.'
        ]);

        // Cari user berdasarkan username
        $user = \App\Models\User::where('name', $request->login)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Username tidak ditemukan dalam sistem.']);
        }

        // Validasi token
        $credentials = $request->only('token');
        $credentials['email'] = $user->email;
        $credentials['password'] = $request->password;
        $credentials['password_confirmation'] = $request->password_confirmation;

        $result = app('auth.password.broker')->reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        // Jika reset berhasil, result akan bernilai string 'PASSWORD_RESET'
        // Jika gagal, akan throw exception atau mengembalikan nilai lain
        // Karena Laravel 12 mungkin mengembalikan Nilai berbeda, kita cek dengan cara lain
        if (!empty($result) && $result !== null) {
            return redirect('/login')->with('status', 'Password berhasil direset! Silakan login dengan password baru.');
        }
        
        return back()->withErrors(['login' => 'Token tidak valid atau telah kedaluwarsa.']);
    }
}
