<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration ini digunakan untuk membuat tabel 'posts' yang biasanya digunakan untuk menyimpan data artikel atau blog post.
class CreatePostsTable extends Migration
{
    /**
     * Method 'up' akan dijalankan ketika menjalankan perintah 'php artisan migrate'.
     * Fungsi ini bertugas untuk membuat struktur tabel 'posts'.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel 'posts'
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Kolom primary key auto-increment dengan nama 'id'
            $table->string('title'); // Kolom untuk menyimpan judul postingan
            $table->string('slug'); // Kolom slug yang biasanya digunakan untuk URL yang SEO friendly
            $table->string('author'); // Kolom untuk menyimpan nama penulis
            $table->string('thumbnail'); // Kolom untuk menyimpan nama file atau path gambar thumbnail
            $table->text('body'); // Kolom isi artikel / isi postingan
            $table->foreignId('category_id'); // Kolom foreign key yang merujuk ke tabel kategori (belum didefinisikan relasinya di sini)
            $table->timestamps(); // Kolom created_at dan updated_at otomatis
        });
    }

    /**
     * Method 'down' akan dijalankan ketika menjalankan perintah 'php artisan migrate:rollback'.
     * Fungsi ini bertugas untuk menghapus tabel 'posts' jika migration dibatalkan.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts'); // Menghapus tabel 'posts' jika ada
    }
}
