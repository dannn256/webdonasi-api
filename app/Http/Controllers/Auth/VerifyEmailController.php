<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Menandai alamat email pengguna yang terautentikasi sebagai telah diverifikasi.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        // Jika email sudah diverifikasi sebelumnya, langsung redirect
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        // Tandai email sebagai telah diverifikasi
        if ($request->user()->markEmailAsVerified()) {
            // Memicu event Verified setelah email berhasil diverifikasi
            event(new Verified($request->user()));
        }

        // Redirect ke halaman utama dengan parameter verified=1
        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }
}
