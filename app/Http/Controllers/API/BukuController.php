<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('penerbit')->get();
        return response()->json([
            'success' => true,
            'data'    => $bukus
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori'     => 'required|string',
            'nama_buku'    => 'required|string',
            'harga'        => 'required|integer',
            'stok'         => 'required|integer',
            'penerbit_id'  => 'required|exists:penerbits,id',
            'foto'         => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('buku', 'public');
        }

        $buku = Buku::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dibuat',
            'data'    => $buku->load('penerbit')
        ], 201);
    }

    public function show($id)
    {
        $buku = Buku::with('penerbit')->find($id);
        if (! $buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $buku
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (! $buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        // Semua field wajib disertakan
        $data = $request->validate([
            'kategori'     => 'required|string',
            'nama_buku'    => 'required|string',
            'harga'        => 'required|integer',
            'stok'         => 'required|integer',
            'penerbit_id'  => 'required|exists:penerbits,id',
            'foto'         => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            if ($buku->foto) {
                Storage::disk('public')->delete($buku->foto);
            }
            $data['foto'] = $request->file('foto')->store('buku', 'public');
        }

        $buku->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui',
            'data'    => $buku->load('penerbit')
        ], 200);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);
        if (! $buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        if ($buku->foto) {
            Storage::disk('public')->delete($buku->foto);
        }

        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ], 200);
    }
}
