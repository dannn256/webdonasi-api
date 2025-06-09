<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Mengarahkan ke tampilan form login
        return view('auth.login');
    }

    /**
     * Menangani permintaan autentikasi masuk (login).
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        // Melakukan proses autentikasi (dengan validasi dari LoginRequest)
        $request->authenticate();

        // Mengatur ulang sesi untuk mencegah session fixation
        $request->session()->regenerate();

        // Redirect ke halaman yang diinginkan setelah login berhasil
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Menghancurkan sesi autentikasi (logout).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Melakukan logout dari guard default ('web')
        Auth::guard('web')->logout();

        // Menghapus semua data sesi
        $request->session()->invalidate();

        // Menghasilkan ulang token CSRF untuk keamanan
        $request->session()->regenerateToken();

        // Redirect ke halaman utama setelah logout
        return redirect('/');
    }
}
