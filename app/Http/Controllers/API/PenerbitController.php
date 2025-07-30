<?php

namespace App\Http\Controllers\API;

use App\Models\Penerbit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Exception;

class PenerbitController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Penerbit::all()], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'    => 'required|string',
            'alamat'  => 'required|string',
            'kota'    => 'required|string',
            'telepon' => 'required|string',
            'foto'    => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('penerbit', 'public');
        }

        $p = Penerbit::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil dibuat',
            'data'    => $p
        ], 201);
    }

    public function show($id)
    {
        $p = Penerbit::find($id);
        if (!$p) {
            return response()->json(['success' => false, 'message' => 'Penerbit tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $p], 200);
    }

    public function update(Request $r, $id)
    {
        $p = Penerbit::find($id);
        if (!$p) {
            return response()->json(['success' => false, 'message' => 'Penerbit tidak ditemukan'], 404);
        }

        $data = $r->validate([
            'nama'    => 'required|string',
            'alamat'  => 'required|string',
            'kota'    => 'required|string',
            'telepon' => 'required|string',
            'foto'    => 'nullable|image|max:2048'
        ]);

        if ($r->hasFile('foto')) {
            // hapus file lama
            if ($p->foto) {
                Storage::disk('public')->delete($p->foto);
            }
            $data['foto'] = $r->file('foto')->store('penerbit', 'public');
        }

        $p->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil diperbarui',
            'data'    => $p
        ], 200);
    }

    public function destroy($id)
    {
        $p = Penerbit::find($id);
        if (!$p) {
            return response()->json(['success' => false, 'message' => 'Penerbit tidak ditemukan'], 404);
        }

        // hapus file foto
        if ($p->foto) {
            Storage::disk('public')->delete($p->foto);
        }

        $p->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil dihapus'
        ], 200);
    }
}
