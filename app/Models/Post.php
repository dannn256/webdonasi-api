<?php

namespace App\Models;

// Mengimpor fitur bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Post mewakili data artikel atau postingan
class Post extends Model
{
    use HasFactory;

    // Daftar kolom yang bisa diisi
    protected $fillable = [
        'title',
        'slug',
        'author',
        'thumbnail',
        'body',
        'category_id'
    ];

    // Relasi: setiap postingan terhubung ke satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
