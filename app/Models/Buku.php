<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model {
    protected $fillable = [
        'kategori','nama_buku','harga','stok',
        'penerbit_id','foto'
    ];

    public function penerbit() {
        return $this->belongsTo(Penerbit::class);
    }
}
