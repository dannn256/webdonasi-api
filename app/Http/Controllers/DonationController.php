<?php

namespace App\Http\Controllers;

use App\Models\{Donation, Campaign};
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::with('campaign')
            ->where('status', 'PAID')
            ->latest()
            ->paginate(10);

        return view('admin.donation.index', compact('donations'));
    }

    public function store(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'address' => 'required|string',
            'amount' => 'required|numeric',
            'phone' => 'required|string',
            'email' => 'required|email'
        ]);

        $donation = Donation::create([
            'name' => $request->name,
            'address' => $request->address,
            'amount' => $request->amount,
            'phone' => $request->phone,
            'campaign_id' => $campaign->id,
            'status' => 'PAID', 
            'payment_method' => 'Manual'
        ]);

        $campaign->increment('raised', $donation->amount);

        return redirect()->route('donation.success', $donation->id);
    }

    public function success(Donation $donation)
    {
        return view('success', compact('donation'));
    }
}
