<?php

namespace App\Http\Controllers;

use App\Models\KategoriPenilaian;
use App\Models\PenilaianBalita;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class KategoriPenilaianController extends Controller
{
    /**
     * Menampilkan data kategori penilaian
     */
    public function index()
    {
        $data = KategoriPenilaian::whereHas('kriteria')
            ->with('kriteria')
            ->orderBy('id', 'asc')
            ->get();

        return view('petugas.kategori_penilaian.index', compact('data'));
    }

    /**
     * Menampilan form tambah kategori penilaian
     */
    public function create() 
    {
        $kriterias = Kriteria::all();

        return view('petugas.kategori_penilaian.create', compact('kriterias'));
    }

    /**
     * Simpan data kategori penilaian sekaligus
     */
    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id'               => 'required|exists:kriterias,id',
            'kategoris'                 => 'required|array|min:1',
            'kategoris.*.nama_kategori' => 'required|string|max:255',
            'kategoris.*.nilai'         => 'required|in:1,3,5',
        ], [
            'kategoris.required'                 => 'Minimal satu kategori harus diisi.',
            'kategoris.*.nama_kategori.required' => 'Nama kategori tidak boleh kosong.',
            'kategoris.*.nilai.required'         => 'Nilai kategori tidak boleh kosong.',
            'kategoris.*.nilai.in'               => 'Nilai harus salah satu dari: 1, 3, atau 5.',
        ]);

        $insert = [];
        foreach ($request->kategoris as $item) {
            $insert[] = [
                'kriteria_id'   => $request->kriteria_id,
                'nama_kategori' => $item['nama_kategori'],
                'nilai'         => $item['nilai'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        KategoriPenilaian::insert($insert);

        return redirect()->route('petugas.kategori_penilaian.index')
            ->with('success', 'Data kategori penilaian berhasil ditambahkan');
    }

    /**
     * menampilkan form edit kategori penilaian 
     */
    public function edit($id)
    {
        $data      = KategoriPenilaian::with(['kriteria' => function ($query) {
            $query->withTrashed();
        }])->findOrFail($id);
        $kriterias = Kriteria::all();

        return view('petugas.kategori_penilaian.edit', compact('data', 'kriterias'));
    }

    /**
     * memperbarui data kategori penilaian 
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kriteria_id'   => 'required|exists:kriterias,id',
            'nama_kategori' => 'required|string|max:255',
            'nilai'         => 'required|in:1,3,5',
        ], [
            'nama_kategori.required' => 'Nama kategori tidak boleh kosong.',
            'nilai.in'               => 'Nilai harus salah satu dari: 1, 3, atau 5.',
        ]);

        $data = KategoriPenilaian::findOrFail($id);

        $data->update([
            'kriteria_id'   => $request->kriteria_id,
            'nama_kategori' => $request->nama_kategori,
            'nilai'         => $request->nilai,
        ]);

        return redirect()->route('petugas.kategori_penilaian.index')
            ->with('success', 'Data kategori penilaian berhasil diperbarui');
    }

    /**
     * menghapus data kategori penilaian 
     */
    public function destroy($id)
    {
        if (PenilaianBalita::where('kategori_penilaian_id', $id)->exists()) {
            return redirect()->route('petugas.kategori_penilaian.index')
                ->with('error', 'Data kategori penilaian tidak dapat dihapus karena sudah digunakan dalam penilaian balita');
        }

        KategoriPenilaian::destroy($id);

        return redirect()->route('petugas.kategori_penilaian.index')
            ->with('success', 'Data kategori penilaian berhasil dihapus');
    }
}