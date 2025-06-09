<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel donations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id(); // Primary key: auto-increment integer
            $table->string('payment_id')->nullable(); // ID dari payment gateway, nullable karena bisa saja belum tersedia saat insert awal
            $table->string('name'); // Nama pendonor
            $table->string('address'); // Alamat pendonor
            $table->string('phone'); // Nomor telepon pendonor
            $table->string('payment_method')->nullable(); // Metode pembayaran (e.g. transfer, QRIS), nullable karena bisa ditentukan nanti
            $table->integer('amount'); // Jumlah donasi dalam satuan mata uang (pastikan disesuaikan dengan kebutuhan: bisa gunakan bigInteger untuk jumlah besar)
            $table->string('status')->nullable(); // Status donasi (e.g. pending, paid), nullable untuk fleksibilitas awal
            $table->foreignId('campaign_id'); // Relasi ke tabel campaigns, pastikan ada constraint foreign key jika perlu
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Mengembalikan (rollback) migrasi dengan menghapus tabel donations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
