<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Menampilkan halaman reset password.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Menampilkan form reset password dengan token dari email
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Menangani permintaan reset password baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Validasi data input: token, email, dan password baru
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        /**
         * Mencoba untuk mereset password:
         * - Jika berhasil, password akan di-hash dan disimpan ke database.
         * - Token "remember me" juga di-reset untuk alasan keamanan.
         * - Event PasswordReset dikirimkan.
         */
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                // Update password dan token pengguna
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Trigger event setelah reset password
                event(new PasswordReset($user));
            }
        );

        /**
         * Redirect sesuai hasil:
         * - Jika berhasil, arahkan ke halaman login dengan pesan sukses.
         * - Jika gagal, kembali ke form dengan error.
         */
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}
