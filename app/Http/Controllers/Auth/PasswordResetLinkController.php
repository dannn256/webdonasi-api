<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Menampilkan halaman permintaan reset password (form lupa password).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Menampilkan form untuk memasukkan email agar mendapatkan link reset password
        return view('auth.forgot-password');
    }

    /**
     * Menangani permintaan pengiriman link reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Validasi bahwa field email diisi dan berformat email
        $request->validate([
            'email' => 'required|email',
        ]);

        /**
         * Mengirimkan link reset password ke email pengguna jika terdaftar.
         * - Jika sukses, beri notifikasi bahwa link telah dikirim.
         * - Jika gagal (misalnya email tidak terdaftar), tampilkan pesan error.
         */
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Menentukan respon berdasarkan hasil pengiriman link reset
        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status)) // Berhasil kirim link
            : back()->withInput($request->only('email')) // Gagal, tampilkan error
                    ->withErrors(['email' => __($status)]);
    }
}
