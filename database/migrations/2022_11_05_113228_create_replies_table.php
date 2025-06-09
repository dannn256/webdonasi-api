<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepliesTable extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel replies.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->text('message'); // Isi balasan dari admin atau sistem
            $table->foreignId('contact_id'); // Relasi ke tabel contacts
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel replies saat rollback.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replies');
    }
}
