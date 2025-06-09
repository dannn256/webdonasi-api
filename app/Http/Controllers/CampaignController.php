<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\{Campaign, Category, Donation};
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    // Menampilkan semua campaign untuk admin
    public function index(Campaign $campaign)
    {
        // Ambil semua data campaign dan donasinya, urutkan dari yang terbaru
        $campaigns = $campaign->with('donations')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.campaign.index', compact('campaigns'));
    }

    // Menampilkan detail campaign ke halaman publik
    public function show(Campaign $campaign)
    {
        // Ambil semua kategori beserta post-nya
        $categories = Category::with('posts')->get();

        // Ambil 5 campaign lain yang berbeda dari yang sedang dibuka
        $campaigns = Campaign::where('slug', '!=', $campaign->slug)
            ->latest()
            ->limit(5)
            ->get();

        // Ambil donasi untuk campaign ini yang statusnya "PAID"
        $donations = Donation::whereHas('campaign', function($query) use ($campaign) {
            return $query
                ->where('campaign_id', $campaign->id)
                ->where('status', 'PAID');
        });

        return view('campaign.show', compact('campaign', 'campaigns', 'categories', 'donations'));
    }

    // Menyimpan data campaign baru dari form admin
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'thumbnail'  => 'required|file|image|mimes:jpg,jpeg,png,svg|max:1024',
            'title' => 'required',
            'body' => 'required',
            'author' => 'required',
            'goals' => 'required|numeric',
            'deadline' => 'required'
        ]);

        $data = $request->all();

        // Buat slug dari judul
        $isExists = Campaign::where('slug', \Str::slug($request->title))->exists();
        $data['slug'] = $isExists
            ? \Str::slug($request->title.'-'.substr(md5(time()), 0, 8))
            : \Str::slug($request->title);

        // Simpan gambar thumbnail jika ada
        if($request->hasFile('thumbnail')){
            $file = $request->file('thumbnail');
            $fileName = $file->getClientOriginalName();
            $folder = Carbon::now()->format('m-d-Y');

            $file->storeAs('campaigns/'.$folder, $fileName, 'public');

            $data['thumbnail'] = 'campaigns/'.$folder.'/'.$fileName;
        }

        // Set dana awal menjadi 0
        $data['raised'] = 0;

        // Simpan campaign ke database
        Campaign::create($data);

        return redirect()->route('admin.campaign.index')->with('success', 'Penggalangan berhasil disimpan!');
    }

    // Menampilkan form edit campaign
    public function edit(Campaign $campaign)
    {
        return view('admin.campaign.edit', compact('campaign'));
    }

    // Menyimpan perubahan pada campaign
    public function update(Request $request, Campaign $campaign)
    {
        // Validasi input
        $request->validate([
            'thumbnail'  => 'nullable|file|image|mimes:jpg,png,svg|max:1024',
            'title' => 'required',
            'body' => 'required',
            'author' => 'required',
            'goals' => 'required|numeric',
            'deadline' => 'required'
        ]);

        $data = $request->all();

        // Simpan thumbnail baru jika ada
        if($request->hasFile('thumbnail')){
            $file = $request->file('thumbnail');
            $fileName = $file->getClientOriginalName();
            $folder = Carbon::now()->format('m-d-Y');

            $file->storeAs('campaigns/'.$folder, $fileName, 'public');

            $data['thumbnail'] = 'campaigns/'.$folder.'/'.$fileName;
        }

        // Update data campaign
        $campaign->update($data);

        return redirect()->route('admin.campaign.index')->with('success', 'Penggalangan berhasil diperbarui');
    }

    // Menghapus campaign dan donasi yang terkait
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        Donation::where('campaign_id', $campaign->id)->delete();

        return back()->with('success', 'Penggalangan berhasil dihapus');
    }

    // Mengambil data donatur untuk campaign tertentu
    public function donatur(Request $request)
    {
        $donations = Donation::select('campaign_id', 'name', 'amount', 'created_at')
            ->whereHas('campaign', function($query) use ($request) {
                return $query
                    ->where('campaign_id', $request->input('id'))
                    ->where('status', 'PAID');
            });

        return $donations->orderBy('created_at', 'desc')->get();
    }
}
