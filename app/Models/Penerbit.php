<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerbit extends Model {
    protected $fillable = ['nama','alamat','kota','telepon','foto'];

    public function bukus() {
        return $this->hasMany(Buku::class);
    }
}

