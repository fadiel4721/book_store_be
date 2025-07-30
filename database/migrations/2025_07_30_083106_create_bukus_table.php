<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('nama_buku');
            $table->integer('harga');
            $table->integer('stok');
            $table->foreignId('penerbit_id')
                  ->constrained('penerbits')
                  ->cascadeOnDelete();
            $table->string('foto')->nullable(); // path ke cover/gambar
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('bukus');
    }
};

