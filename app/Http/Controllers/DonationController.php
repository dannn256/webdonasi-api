<?php

namespace App\Http\Controllers;

use App\Models\{Donation, Campaign};
use Illuminate\Http\Request;

class DonationController extends Controller
{
    // Tampilkan daftar donasi yang sudah dibayar, terbaru, dengan 10 data per halaman
    public function index()
    {
        $donations = Donation::with('campaign') // ambil data campaign yang terkait
            ->where('status', 'PAID') // hanya donasi yang sudah dibayar
            ->latest()
            ->paginate(10);

        return view('admin.donation.index', compact('donations'));
    }

    // Simpan data donasi baru yang masuk ke campaign tertentu
    public function store(Request $request, Campaign $campaign)
    {
        // Validasi inputan dari form donasi
        $request->validate([
            'name' => 'required|string|max:20',
            'address' => 'required|string',
            'amount' => 'required|numeric',
            'phone' => 'required|string',
            'email' => 'required|email'
        ]);

        // Buat data donasi baru, status langsung "PAID" dan metode manual
        $donation = Donation::create([
            'name' => $request->name,
            'address' => $request->address,
            'amount' => $request->amount,
            'phone' => $request->phone,
            'campaign_id' => $campaign->id,
            'status' => 'PAID', 
            'payment_method' => 'Manual'
        ]);

        // Tambahkan nominal donasi ke total dana terkumpul campaign
        $campaign->increment('raised', $donation->amount);

        // Redirect ke halaman sukses donasi
        return redirect()->route('donation.success', $donation->id);
    }

    // Tampilkan halaman sukses setelah donasi berhasil
    public function success(Donation $donation)
    {
        return view('success', compact('donation'));
    }
}
