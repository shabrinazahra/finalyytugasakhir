<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\PenilaianBalita;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index() //menampilkan data kriteria
    {
        $kriterias = Kriteria::orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')->get();
        return view('petugas.kriteria.index', compact('kriterias'));
    }

    public function create() //menampilkan form tambah kriteria 
    { {
            $kode_otomatis = $this->generateKode();
            return view('petugas.kriteria.create', compact('kode_otomatis'));
        }
    }

    public function store(Request $request) //menyimpan data kriteria baru 
    {
        $request->validate([
            'nama_kriteria' => 'required',
            'atribut'       => 'required',
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'atribut.required'       => 'Atribut wajib dipilih.',
        ]);

        // Generate kode di server agar tidak bisa dimanipulasi dari form
        $kode = $this->generateKode();

        Kriteria::create([
            'kode_kriteria' => $kode,
            'nama_kriteria' => $request->nama_kriteria,
            'atribut'       => $request->atribut,
        ]);

        return redirect()->route('petugas.kriteria.index')
            ->with('success', 'Kriteria ' . $kode . ' berhasil ditambahkan');
    }

    public function edit($id) //menampilkan form edit kriteria
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('petugas.kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, $id) //memperbarui data kriteria 
    {
        $request->validate([
            'nama_kriteria' => 'required',
            'atribut'       => 'required',
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'atribut.required'       => 'Atribut wajib dipilih.',
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'atribut'       => $request->atribut,
        ]);

        return redirect()->route('petugas.kriteria.index')
            ->with('success', 'Data kriteria berhasil diperbarui');
    }

    public function destroy($id) // menghapus data kriteria 
    {
        if (PenilaianBalita::where('kriteria_id', $id)->exists()) {
            return redirect()->route('petugas.kriteria.index')
                ->with('error', 'Data kriteria tidak dapat dihapus karena sudah digunakan dalam penilaian balita');
        }
        
        Kriteria::destroy($id);

        return redirect()->route('petugas.kriteria.index')
            ->with('success', 'Data kriteria berhasil dihapus');
    }

    private function generateKode(): string //menghasialkan kode kriteria baru secara otomatis 
    {
        // Ambil semua nomor dari kode yang ada, misal K1→1, K3→3 
        $used = Kriteria::pluck('kode_kriteria')
            ->map(fn($k) => (int) str_replace('K', '', $k))
            ->toArray();

        $next = 1;
        while (in_array($next, $used)) {
            $next++;
        }

        return 'K' . $next;
    }
}
