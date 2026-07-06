<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalitaController extends Controller
{
    public function index(Request $request) //menampilkan data baliita sesuai posyandu user 
    {
        $user = Auth::user();

        $query = Balita::with('posyandu')
            ->where('posyandu_id', $user->posyandu_id) //menampilkan balita sesuai posyandu user 
            ->oldest();

        // SEARCH
        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');

            // tampilkan semua hasil search
            $balitas = $query->latest()->get();
        } else {
            // pagination normal
            $balitas = $query->latest()->paginate(10);
        }

        return view('kader.balita.index', compact('balitas'));
    }

    public function create() //menampilkan form tambah  balita 
    {
        return view('kader.balita.create');
    }

    public function store(Request $request) //menyimpan data baliita baru 
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => 'required|string|max:20',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'nama_ortu'     => 'required|string|max:255',
        ], [
            'nama.required'          => 'Nama balita wajib diisi.',
            'nama.max'               => 'Nama balita maksimal 255 karakter.',
            'nik.required'           => 'NIK wajib diisi.',
            'nik.max'                => 'NIK maksimal 20 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date'     => 'Format tanggal lahir tidak valid.',
            'nama_ortu.required'     => 'Nama orang tua wajib diisi.',
            'nama_ortu.max'          => 'Nama orang tua maksimal 255 karakter.',
        ]);

        $user = Auth::user(); //ambil user

        // pastikan user punya posyandu
        if (!$user->posyandu_id) {
            return back()->with('error', 'User belum memiliki posyandu');
        }

        Balita::create([ //menyimpan data balita baru
            'nama' => $request->nama,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nama_ortu' => $request->nama_ortu,
            'posyandu_id' => $user->posyandu_id, // 
        ]);

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil ditambahkan');
    }

    public function edit($id)//menampilkan from edit balita
    {
        $user = Auth::user();

        // hanya bisa akses data posyandu sendiri
        $balita = Balita::where('id', $id)
            ->where('posyandu_id', $user->posyandu_id)
            ->firstOrFail();

        return view('kader.balita.edit', compact('balita'));
    }

    public function update(Request $request, $id) //memperbarui data balita 
    { 
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:20',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'nama_ortu' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $balita = Balita::where('id', $id)
            ->where('posyandu_id', $user->posyandu_id)
            ->firstOrFail();

        $balita->update([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nama_ortu' => $request->nama_ortu,
        ]);

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil diperbarui');
    }

    public function destroy($id) //menghapus data balita 
    {
        $user = Auth::user();

        $balita = Balita::where('id', $id)
            ->where('posyandu_id', $user->posyandu_id)
            ->firstOrFail();

        $balita->delete();

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil dihapus');
    }
}
