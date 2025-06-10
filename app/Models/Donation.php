<?php 

namespace App\Models;

// Mengimpor fitur bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Donation mewakili data donasi dari pengguna
class Donation extends Model
{
    use HasFactory;

    // Daftar kolom yang bisa diisi
    protected $fillable = [
        'name',
        'address',
        'payment_method',
        'amount',
        'phone',
        'status',
        'campaign_id',
        'payment_id'
    ];

    // Relasi: setiap donasi terhubung ke satu kampanye
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
