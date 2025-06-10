<?php

namespace App\Models;

// Mengimpor fitur bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Mendefinisikan model Category, mewakili data kategori di database
class Category extends Model
{
    // Mengaktifkan fitur factory untuk pembuatan data palsu saat testing
    use HasFactory;

    // Menentukan kolom yang boleh diisi saat membuat atau mengubah data kategori
    protected $fillable = [
        'name',
        'slug'
    ];

    // Relasi: Satu kategori bisa memiliki banyak postingan
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
