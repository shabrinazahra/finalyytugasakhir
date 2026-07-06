<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index() // menampilkan daftar posyandu dan kader yang terkait
    {
        $posyandus = Posyandu::with('kader')->get();
        return view('master_admin.posyandu.index', compact('posyandus'));
    }

    public function create() //menampilkan form untuk menambah data posyandu baru
    {
        return view('master_admin.posyandu.create');
    }

    public function store(Request $request) //menyimpan data posyandu baru
    {
        $request->validate([
            'nama_posyandu' => 'required',
            'alamat' => 'nullable',
        ]);

        Posyandu::create($request->all());

        return redirect()->route('posyandu.index')->with('success', 'Data Posyandu berhasil ditambahkan');
    }

    public function edit($id) //menampilkan form edit berdasarkan id posyandu yang dipilih
    {
        $posyandu = Posyandu::findOrFail($id);
        return view('master_admin.posyandu.edit', compact('posyandu'));
    }

    public function update(Request $request, $id) //memperbarui data posyandu  yang dipilih
    {
        $request->validate([
            'nama_posyandu' => 'required',
            'alamat' => 'nullable',
        ]);

        $posyandu = Posyandu::findOrFail($id);
        $posyandu->update($request->all());

        return redirect()->route('posyandu.index')->with('success', 'Data Posyandu berhasil diperbarui');
    }

    public function destroy($id) //menghapus data posyandu yang dipilih
    {
        $posyandu = Posyandu::findOrFail($id);
        $posyandu->delete();

        return redirect()->route('posyandu.index')->with('success', 'Data Posyandu berhasil dihapus');
    }
}
