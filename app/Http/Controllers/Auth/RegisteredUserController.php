<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi pengguna baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Menampilkan view form registrasi
        return view('auth.register');
    }

    /**
     * Menangani permintaan registrasi pengguna baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Validasi data input dari form registrasi
        $request->validate([
            'name' => 'required|string|max:255', // Nama wajib diisi dan maksimal 255 karakter
            'email' => 'required|string|email|max:255|unique:users', // Email unik dan valid
            'password' => 'required|string|confirmed|min:8', // Password minimal 8 karakter dan dikonfirmasi
        ]);

        // Membuat user baru dan menyimpan ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // Memicu event Registered, misalnya untuk verifikasi email
        event(new Registered($user));

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        // Redirect ke halaman utama setelah login
        return redirect(RouteServiceProvider::HOME);
    }
}
