<?php

namespace App\Models;

// Mengimpor fitur bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Reply mewakili data balasan terhadap pesan dari pengguna
class Reply extends Model
{
    use HasFactory;

    // Daftar kolom yang bisa diisi
    protected $fillable = [
        'message',
        'contact_id'
    ];

    // Relasi: setiap balasan terhubung ke satu pesan kontak
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
