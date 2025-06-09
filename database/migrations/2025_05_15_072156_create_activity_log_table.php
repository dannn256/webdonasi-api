<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Migration ini digunakan untuk membuat tabel log aktivitas (activity log).
// Biasanya dipakai untuk mencatat setiap perubahan data, seperti log audit.
class CreateActivityLogTable extends Migration
{
    //Method 'up' dijalankan ketika perintah 'php artisan migrate' dieksekusi.
     //Fungsi ini akan membuat tabel activity log sesuai konfigurasi.
    public function up()
    {
        // Membuat tabel log aktivitas dengan koneksi database dan nama tabel yang diambil dari file konfigurasi
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key auto-increment
            $table->string('log_name')->nullable(); // Nama log, bisa dikelompokkan berdasarkan jenis log, boleh kosong
            $table->text('description'); // Deskripsi aktivitas yang terjadi

            // Relasi polymorphic (nullable) untuk subjek yang dipengaruhi aktivitas (contoh: post, user, order)
            $table->nullableMorphs('subject', 'subject');

            // Relasi polymorphic (nullable) untuk pelaku aktivitas (causer), bisa user atau sistem
            $table->nullableMorphs('causer', 'causer');

            $table->json('properties')->nullable(); // Data tambahan dalam format JSON (contoh: isi perubahan, metadata)
            $table->timestamps(); // Kolom created_at dan updated_at otomatis
            $table->index('log_name'); // Index pada kolom log_name untuk mempercepat pencarian berdasarkan nama log
        });
    }

    //Method 'down' dijalankan ketika perintah 'php artisan migrate:rollback' dieksekusi.
    //Fungsi ini akan menghapus tabel log aktivitas jika migration dibatalkan.
    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name')); // Menghapus tabel log aktivitas jika ada
    }
}
