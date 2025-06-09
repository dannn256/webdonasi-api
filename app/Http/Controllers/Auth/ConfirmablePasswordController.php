<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Menampilkan halaman konfirmasi password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Menampilkan view untuk konfirmasi password pengguna
        return view('auth.confirm-password');
    }

    /**
     * Memverifikasi password pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        // Validasi kredensial (password) pengguna saat ini
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            // Jika validasi gagal, kirim pesan error ke form
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Simpan waktu konfirmasi password ke dalam session
        $request->session()->put('auth.password_confirmed_at', time());

        // Redirect ke halaman tujuan setelah konfirmasi berhasil
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
