<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration ini digunakan untuk membuat tabel 'failed_jobs'.
// Tabel ini berfungsi untuk mencatat semua pekerjaan (jobs) yang gagal diproses oleh queue Laravel.
class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel 'failed_jobs'
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     * Method 'down' akan dijalankan ketika menjalankan perintah 'php artisan migrate:rollback'.
     * Fungsi ini digunakan untuk menghapus tabel 'failed_jobs' jika migration dibatalkan.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_jobs'); /// Menghapus tabel 'failed_jobs' jika ada
    }
}
