<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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

    public function show($id)
    {
        return redirect()->route('balita.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.file' => 'File yang diunggah tidak valid.',
            'file.mimes' => 'Format file harus Excel atau CSV.',
        ]);

        $user = Auth::user();

        if (!$user->posyandu_id) {
            return back()->with('error', 'User belum memiliki posyandu');
        }

        $file = $request->file('file');
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return back()->with('error', 'File Excel tidak memiliki data baris yang cukup.');
        }

        $header = array_map(fn($value) => strtolower(trim((string) $value)), $rows[0]);
        $expected = ['nama', 'nik', 'jenis kelamin', 'tanggal lahir', 'nama orang tua'];

        foreach ($expected as $field) {
            if (!in_array($field, $header, true)) {
                return back()->with('error', 'Format file tidak sesuai. Pastikan kolom: Nama, NIK, Jenis Kelamin, Tanggal Lahir, Nama Orang Tua.');
            }
        }

        $imported = 0;
        foreach (array_slice($rows, 1) as $row) {
            $data = array_combine($header, $row);
            if (!$data) {
                continue;
            }

            $nama = trim((string) ($data['nama'] ?? ''));
            $nik = trim((string) ($data['nik'] ?? ''));
            $jenisKelamin = trim((string) ($data['jenis kelamin'] ?? ''));
            $tanggalLahir = trim((string) ($data['tanggal lahir'] ?? ''));
            $namaOrtu = trim((string) ($data['nama orang tua'] ?? ''));

            if ($nama === '' || $nik === '' || $jenisKelamin === '' || $tanggalLahir === '' || $namaOrtu === '') {
                continue;
            }

            $formattedDate = null;
            if (is_numeric($tanggalLahir)) {
                $formattedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $tanggalLahir)->format('Y-m-d');
            } else {
                try {
                    $formattedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalLahir)->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        $formattedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalLahir)->format('Y-m-d');
                    } catch (\Exception $e2) {
                        try {
                            $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalLahir)->format('Y-m-d');
                        } catch (\Exception $e3) {
                            $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->format('Y-m-d');
                        }
                    }
                }
            }

            Balita::create([
                'nama' => $nama,
                'nik' => $nik,
                'jenis_kelamin' => $jenisKelamin,
                'tanggal_lahir' => $formattedDate,
                'nama_ortu' => $namaOrtu,
                'posyandu_id' => $user->posyandu_id,
            ]);

            $imported++;
        }

        return redirect()->route('balita.index')->with('success', "Berhasil mengimpor $imported data balita");
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
            'posyandu_id' => $user->posyandu_id,
        ]);

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil ditambahkan');
    }

    public function edit($id) //menampilkan from edit balita
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
