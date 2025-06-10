<?php

namespace App\Models;

// Mengimpor berbagai fitur dari Laravel dan package tambahan
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;

// Model User mewakili data pengguna sistem
class User extends Authenticatable
{
    // Mengaktifkan fitur bawaan seperti notifikasi, role, dan pencatatan aktivitas
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    // Kolom yang boleh diisi
    protected $fillable = [
        'name',
        'username',
        'password',
        'avatar'
    ];

    // Konfigurasi log aktivitas pengguna
    protected static $logAttributes = ['name', 'username'];
    protected static $igonoreChangedAttributes = ['updated_at'];
    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'user';

    // Kolom yang disembunyikan saat data ditampilkan
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konversi otomatis tipe data tertentu
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Menyediakan deskripsi khusus saat aktivitas (create, update, delete) terjadi
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} user";
    }

    // Menandai apakah pengguna memiliki peran Admin
    public function getIsAdminAttribute()
    {
        return $this->hasRole('Admin');
    }
}
