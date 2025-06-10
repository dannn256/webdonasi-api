<?php

namespace App\Models;

// Mengimpor pustaka untuk menangani tanggal dan waktu
use Carbon\Carbon;

// Mengimpor fitur-fitur dari Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Mendefinisikan model Campaign, mewakili data kampanye di database
class Campaign extends Model
{
    // Mengaktifkan fitur factory untuk pembuatan data palsu saat testing
    use HasFactory;

    // Menentukan kolom-kolom yang boleh diisi saat membuat atau mengubah data kampanye
    protected $fillable = [
        'title',
        'slug',
        'author',
        'thumbnail',
        'body',
        'goals',
        'raised',
        'deadline'
    ];

    // Fungsi ini akan mengubah tampilan 'deadline' menjadi format yang lebih mudah dipahami
    public function getDeadlineAttribute($value)
    {
        // Menghitung selisih waktu dari sekarang ke deadline dalam hari, bulan, dan jam
        $days = Carbon::create($value)->diffInDays();
        $months = Carbon::create($value)->diffInMonths()." bulan lagi";
        $hours = Carbon::create($value)->diffInHours()." jam lagi";

        // Menentukan cara menampilkan deadline berdasarkan jumlah hari tersisa
        if($days <= 30) return $days." hari lagi";
        if($days < 1) return $hours;

        return $months;
    }

    // Relasi: Satu kampanye bisa memiliki banyak donasi
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
