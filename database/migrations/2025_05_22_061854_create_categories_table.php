<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel categories.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary key: auto-increment
            $table->string('name'); // Nama kategori (e.g. Pendidikan, Kesehatan)
            $table->string('slug'); // Slug untuk URL-friendly version dari nama
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel categories saat rollback migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
