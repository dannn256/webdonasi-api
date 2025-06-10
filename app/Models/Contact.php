<?php

namespace App\Models;

// Mengimpor fitur bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Contact mewakili data pesan dari pengguna
class Contact extends Model
{
    use HasFactory;

    // Daftar kolom yang bisa diisi
    protected $fillable = [
        'subject',
        'message',
        'name',
        'email',
        'phone',
        'status'
    ];

    // Relasi: satu pesan bisa memiliki banyak balasan
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
