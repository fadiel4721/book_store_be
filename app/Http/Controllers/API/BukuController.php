<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    // 1. Semua buku (public-authenticated)
    public function index()
    {
        $bukus = Buku::with('penerbit')->get();
        return response()->json(['success'=>true,'data'=>$bukus], 200);
    }

    // 2. Detail satu buku
    public function show($id)
    {
        $buku = Buku::with('penerbit')->find($id);
        if (!$buku) {
            return response()->json(['success'=>false,'message'=>'Buku tidak ditemukan'], 404);
        }
        return response()->json(['success'=>true,'data'=>$buku], 200);
    }

    // 3. Daftar buku milik penerbit yang login
    public function indexByPenerbit()
    {
        $userId = auth()->id();
        $bukus = Buku::with('penerbit')
                     ->where('penerbit_id', $userId)
                     ->get();
        return response()->json(['success'=>true,'data'=>$bukus], 200);
    }

    // 4. Detail buku milik penerbit
    public function showByPenerbit($id)
    {
        $userId = auth()->id();
        $buku = Buku::with('penerbit')
                    ->where('penerbit_id', $userId)
                    ->find($id);
        if (!$buku) {
            return response()->json(['success'=>false,'message'=>'Buku tidak ditemukan'], 404);
        }
        return response()->json(['success'=>true,'data'=>$buku], 200);
    }

    // 5. Buat buku baru (admin atau penerbit)
    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori'  => 'required|string',
            'nama_buku' => 'required|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer',
            'foto'      => 'nullable|image|max:2048',
        ]);

        // Tentukan penerbit_id sesuai user yang login jika role=penerbit
        if (auth()->user()->role->name === 'penerbit') {
            $data['penerbit_id'] = auth()->id();
        } else {
            // admin wajib kirim penerbit_id
            $data['penerbit_id'] = $request->validate([
                'penerbit_id' => 'required|exists:penerbits,id'
            ])['penerbit_id'];
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('buku','public');
        }

        $buku = Buku::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dibuat',
            'data'    => $buku->load('penerbit')
        ], 201);
    }

    // 6. Update buku (admin atau penerbit)
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['success'=>false,'message'=>'Buku tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'kategori'  => 'required|string',
            'nama_buku' => 'required|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer',
            'foto'      => 'nullable|image|max:2048',
        ]);

        // Cek kepemilikan untuk role=penerbit
        if (auth()->user()->role->name === 'penerbit') {
            if ($buku->penerbit_id !== auth()->id()) {
                return response()->json(['success'=>false,'message'=>'Unauthorized'], 403);
            }
        } else {
            // admin boleh ubah penerbit_id
            $data['penerbit_id'] = $request->validate([
                'penerbit_id' => 'required|exists:penerbits,id'
            ])['penerbit_id'];
        }

        if ($request->hasFile('foto')) {
            if ($buku->foto) {
                Storage::disk('public')->delete($buku->foto);
            }
            $data['foto'] = $request->file('foto')->store('buku','public');
        }

        $buku->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui',
            'data'    => $buku->load('penerbit')
        ], 200);
    }

    // 7. Hapus buku (admin atau penerbit)
    public function destroy($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['success'=>false,'message'=>'Buku tidak ditemukan'], 404);
        }

        // Cek kepemilikan untuk penerbit
        if (auth()->user()->role->name === 'penerbit' && $buku->penerbit_id !== auth()->id()) {
            return response()->json(['success'=>false,'message'=>'Unauthorized'], 403);
        }

        if ($buku->foto) {
            Storage::disk('public')->delete($buku->foto);
        }

        $buku->delete();

        return response()->json(['success'=>true,'message'=>'Buku berhasil dihapus'], 200);
    }
}
