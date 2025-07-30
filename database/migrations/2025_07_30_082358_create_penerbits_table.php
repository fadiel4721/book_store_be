<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('penerbits', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat');
            $table->string('kota');
            $table->string('telepon');
            $table->string('foto')->nullable(); // path ke gambar
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('penerbits');
    }
};
