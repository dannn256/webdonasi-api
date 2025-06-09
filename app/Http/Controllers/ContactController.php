<?php

namespace App\Http\Controllers;

use App\Models\{Contact, Reply};
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Menampilkan daftar kontak, bisa difilter berdasarkan status
    public function index()
    {
        // Jika filter bukan "all", ambil kontak berdasarkan status filter, batasi 10 data terbaru
        if(request('filter') !== 'all') {
            $contacts = Contact::where('status', request('filter'))->latest()->limit(10)->get();
        }

        // Jika filter "all" atau tidak ada filter, ambil 10 kontak terbaru semua status
        if(request('filter') === 'all' || request('filter') === null) {
            $contacts = Contact::latest()->limit(10)->get();
        }

        // Tampilkan data kontak ke halaman admin
        return view('admin.contact.index', compact('contacts'));
    }

    // Menyimpan pesan kontak yang dikirim oleh user
    public function store(Request $request)
    {
        // Validasi data input agar sesuai aturan
        $request->validate([
            'subject' => 'required|string|max:20,min:5',  // Judul wajib, panjang 5-20 karakter
            'message' => 'required|string',               // Pesan wajib diisi
            'name' => 'required|string|max:40',           // Nama wajib, maksimal 40 karakter
            'email' => 'required|email',                   // Email wajib dan harus valid
            'phone' => 'required|string',                  // Nomor telepon wajib
        ]);

        $data = $request->all();

        // Status 0 berarti pesan baru dan belum dibaca/admin belum membalas
        $data['status'] = 0;

        // Simpan data kontak ke database
        Contact::create($data);

        // Kirim email notifikasi ke admin (email & isi email hardcoded disini)
        $this->composeEmail([
            'email' => 'darulirfan20@gmail.com',
            'name' => 'me',
            'subject' => $request->subject,
            'message' => 'Pengirim: <b>'.$request->name.'</b><br><br>'.nl2br($request->message)
        ]);

        return 'success';
    }

    // Menampilkan detail pesan kontak di halaman admin
    public function show(Contact $contact)
    {
        // Jika pesan belum dibaca (status 0), ubah status jadi 1 (sudah dibaca)
        if($contact->status == 0) {
            $contact->update(['status' => 1]);
        }

        // Tampilkan halaman detail pesan kontak
        return view('admin.contact.show', compact('contact'));
    }

    // Mengirim balasan pesan ke kontak dan simpan balasan di database
    public function reply(Request $request, Contact $contact)
    {
        // Validasi isi pesan balasan wajib diisi
        $request->validate(['message' => 'required|string']);

        // Buat isi email balasan dengan format pesan asli dan pesan balasan
        $response = $this->composeEmail([
            'email' => $contact->email,
            'name' => $contact->name,
            'subject' => "Balasan: ".$contact->subject,
            'message' => "<div style='border: 1px solid gray; padding: 10px; margin-bottom: 10px;'>"
                         .nl2br($contact->message)."</div>".nl2br($request->message)
        ]);

        // Jika email gagal terkirim, tampilkan pesan error
        if(!$response) {
            return back()->with('failed', 'Terjadi kesalahan!');
        }

        // Simpan balasan ke tabel replies dengan relasi ke kontak yang bersangkutan
        Reply::create([
            'message' => $request->message,
            'contact_id' => $contact->id
        ]);

        // Update status kontak menjadi 2 (sudah dibalas)
        $contact->update(['status' => 2]);

        return back()->with('success', 'Pesan telah dikirim!');
    }

    // Menghapus satu pesan kontak
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect(route('admin.contact.index'))->with('success', 'Pesan berhasil dihapus');
    }

    // Menghapus banyak pesan sekaligus berdasarkan daftar id yang dipilih
    public function bulkDelete(Request $request)
    {
        // Hapus semua kontak dengan id yang ada di request
        $contacts = Contact::whereIn('id', $request->id)->delete();

        if($contacts > 0) {
            return redirect(route('admin.contact.index'))->with('success', "$contacts pesan berhasil dihapus!");
        }

        return redirect(route('admin.contact.index'))->with('failed', "Gagal menghapus pesan");
    }
}
