<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel contacts.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('subject'); // Subjek pesan dari pengguna
            $table->text('message'); // Isi pesan/kritik/saran
            $table->string('name'); // Nama pengirim
            $table->string('email'); // Email pengirim
            $table->string('phone'); // Nomor telepon pengirim
            $table->integer('status'); // Status (misalnya: 0 = belum dibaca, 1 = dibaca, 2 = ditindaklanjuti)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel contacts saat rollback.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
