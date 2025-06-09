<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

// Migration ini digunakan untuk membuat tabel 'settings' (nama tabel diatur dari file konfigurasi).
class CreateSettingsTable extends Migration
{
    /**
     * Set up the options.
     */
    public function __construct()
    {
        $this->table = config('setting.database.table');
        $this->key = config('setting.database.key');
        $this->value = config('setting.database.value');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel sesuai nama yang diatur di konfigurasi
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string($this->key)->index()->nullable();
            $table->text($this->value)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus tabel sesuai nama yang diambil dari konfigurasi
        Schema::drop($this->table);
    }
}
