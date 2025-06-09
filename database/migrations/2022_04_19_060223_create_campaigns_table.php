<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel campaigns.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id(); // Primary key: auto-increment
            $table->string('title'); // Judul kampanye
            $table->string('slug'); // Slug untuk URL-friendly dari title
            $table->string('author'); // Nama pembuat kampanye (bisa juga foreign key jika dihubungkan ke users)
            $table->string('thumbnail'); // Path gambar thumbnail kampanye
            $table->text('body'); // Deskripsi detail kampanye
            $table->integer('goals'); // Target dana (jumlah yang ingin dicapai)
            $table->integer('raised'); // Jumlah dana yang sudah terkumpul
            $table->date('deadline'); // Tanggal akhir kampanye
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel campaigns saat rollback.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
