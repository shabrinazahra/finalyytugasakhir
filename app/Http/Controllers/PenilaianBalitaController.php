<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Balita;
use App\Models\Kriteria;
use App\Models\PenilaianBalita;
use App\Models\KategoriPenilaian;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PenilaianBalitaController extends Controller
{
    // =========================
    // INDEX
    // =========================

    public function index(Request $request) // menampilkan daftar penilaian balita
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Ambil filter bulan & tahun dari bulan & tahun sekarang
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Filter berdasarkan bulan dan tahun penilaian
        $query = PenilaianBalita::with(['balita', 'kriteria', 'kategori'])
            ->whereHas('balita', function ($q) use ($user) {
                $q->where('posyandu_id', $user->posyandu_id);
            })
            ->whereMonth('tanggal_penilaian', $bulan)
            ->whereYear('tanggal_penilaian', $tahun);

        // Jika ada kata pencarian, filter tambahan berdasarkan nama balita
        if ($request->filled('search')) {
            $query->whereHas('balita', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // Ambil semua data lalu kelompokkan berdasarkan balita_id
        // Sehingga setiap balita memiliki satu grup berisi semua penilaian kriterianya
        $allData = $query->get()->groupBy('balita_id');

        // Ambil daftar kriteria, diurutkan secara numerik berdasarkan angka di kode_kriteria
        // Contoh: C1, C2, ..., C10 (bukan urutan teks C1, C10, C2)
        $kriterias = Kriteria::orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')->get();

        $page = $request->input('page', 1);
        $perPage = 10; // Tampilkan 10 balita per halaman
        $penilaians = new LengthAwarePaginator(
            $allData->slice(($page - 1) * $perPage, $perPage), // Potong data sesuai halaman
            $allData->count(),   // Total jumlah balita
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // Agar link pagination benar
        );

        // Ambil daftar tahun unik dari data penilaian (untuk dropdown filter tahun di view)
        $tahunList = PenilaianBalita::whereHas('balita', function ($q) use ($user) {
            $q->where('posyandu_id', $user->posyandu_id);
        })
            ->selectRaw('YEAR(tanggal_penilaian) as tahun')
            ->distinct()
            ->pluck('tahun');

        // Jika belum ada data penilaian, default ke tahun sekarang
        if ($tahunList->isEmpty()) {
            $tahunList = collect([now()->year]);
        }

        // Kirim data ke view untuk ditampilkan
        return view('kader.penilaian_balita.index', compact(
            'penilaians',
            'kriterias',
            'bulan',
            'tahun',
            'tahunList'
        ));
    }

    // =========================
    // CREATE
    // =========================
    public function create() //menampilkan form tambah penilaian balita
    {
        // Ambil semua kriteria beserta relasi kategori penilaiannya
        // Diurutkan secara numerik berdasarkan kode kriteria
        $kriterias = Kriteria::with('kategoriPenilaians')
            ->orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')
            ->get();

        return view('kader.penilaian_balita.create', compact('kriterias'));
    }

    private function normalizeHeader(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/', '', mb_strtolower(trim($value)));
    }

    private function findHeaderIndex(array $headers, array $candidates): ?int
    {
        $normalizedCandidates = array_map(fn($value) => $this->normalizeHeader($value), $candidates);

        foreach ($headers as $index => $header) {
            if (in_array($this->normalizeHeader($header), $normalizedCandidates, true)) {
                return $index;
            }
        }

        return null;
    }

    private function getCellValue(array $headers, array $row, array $candidates): string
    {
        $index = $this->findHeaderIndex($headers, $candidates);

        if ($index === null) {
            return '';
        }

        return trim((string) ($row[$index] ?? ''));
    }

    private function findBalitaByName(string $namaBalita, int $posyanduId): ?Balita
    {
        $normalizedTarget = $this->normalizeHeader($namaBalita);

        $balitas = Balita::where('posyandu_id', $posyanduId)->get();

        foreach ($balitas as $balita) {
            if ($this->normalizeHeader($balita->nama) === $normalizedTarget) {
                return $balita;
            }
        }

        return null;
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

        $headers = array_values(array_map(fn($value) => trim((string) $value), $rows[0]));

        $namaIndex = $this->findHeaderIndex($headers, ['Nama Balita', 'Nama', 'Balita', 'Nama Anak']);
        $tanggalIndex = $this->findHeaderIndex($headers, ['Tanggal Penilaian', 'Tanggal', 'Tgl Penilaian', 'Tgl']);

        if ($namaIndex === null || $tanggalIndex === null) {
            return back()->with('error', 'Format file tidak sesuai. Pastikan ada kolom Nama Balita dan Tanggal Penilaian.');
        }

        $kriterias = Kriteria::orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')->get();
        $imported = 0;
        $firstImportedDate = null;

        foreach (array_slice($rows, 1) as $row) {
            $namaBalita = trim((string) ($row[$namaIndex] ?? ''));
            $tanggalPenilaian = trim((string) ($row[$tanggalIndex] ?? ''));

            if ($namaBalita === '' || $tanggalPenilaian === '') {
                continue;
            }

            $balita = $this->findBalitaByName($namaBalita, $user->posyandu_id);
            if (!$balita) {
                continue;
            }

            $tanggal = null;
            try {
                $tanggal = Carbon::createFromFormat('d/m/Y', $tanggalPenilaian);
            } catch (\Exception $e) {
                try {
                    $tanggal = Carbon::createFromFormat('Y-m-d', $tanggalPenilaian);
                } catch (\Exception $e2) {
                    try {
                        $tanggal = Carbon::createFromFormat('d-m-Y', $tanggalPenilaian);
                    } catch (\Exception $e3) {
                        $tanggal = Carbon::parse($tanggalPenilaian);
                    }
                }
            }

            $existing = PenilaianBalita::where('balita_id', $balita->id)
                ->whereMonth('tanggal_penilaian', $tanggal->month)
                ->whereYear('tanggal_penilaian', $tanggal->year)
                ->exists();

            if ($existing) {
                continue;
            }

            $hasInserted = false;
            foreach ($kriterias as $kriteria) {
                $cellValue = $this->getCellValue($headers, $row, [$kriteria->nama_kriteria, $kriteria->kode_kriteria]);
                if ($cellValue === '') {
                    continue;
                }

                $kategori = KategoriPenilaian::where('kriteria_id', $kriteria->id)
                    ->whereRaw('LOWER(TRIM(nama_kategori)) = ?', [mb_strtolower(trim($cellValue))])
                    ->first();

                if (!$kategori) {
                    continue;
                }

                PenilaianBalita::create([
                    'balita_id' => $balita->id,
                    'kriteria_id' => $kriteria->id,
                    'kategori_penilaian_id' => $kategori->id,
                    'tanggal_penilaian' => $tanggal->format('Y-m-d'),
                    'bobot_snapshot' => $kriteria->bobot,
                    'nilai_kategori_snapshot' => $kategori->nilai,
                ]);

                $hasInserted = true;
            }

            if ($hasInserted) {
                $imported++;
                if ($firstImportedDate === null) {
                    $firstImportedDate = $tanggal; // <-- tambahkan ini
                }
            }
        }

        return redirect()->route('penilaian_balita.index', $firstImportedDate ? [
            'bulan' => $firstImportedDate->month,
            'tahun' => $firstImportedDate->year,
        ] : [])->with('success', "Berhasil mengimpor $imported data penilaian");
    }

    // =========================
    // BALITA TERSEDIA
    // =========================
    public function balitaTersedia(Request $request) //mengambil daftar balita yang belum dinilai pada bulan & tahun tertentu
    {
        $user = Auth::user();

        // Parse tanggal yang dikirim dari frontend
        $tanggal = Carbon::parse($request->tanggal);

        // Cari ID balita yang sudah pernah dinilai pada bulan & tahun dari tanggal tersebut
        $sudahDinilai = PenilaianBalita::whereHas('balita', function ($q) use ($user) {
            $q->where('posyandu_id', $user->posyandu_id);
        })
            ->whereMonth('tanggal_penilaian', $tanggal->month)
            ->whereYear('tanggal_penilaian', $tanggal->year)
            ->pluck('balita_id')
            ->unique()
            ->toArray();

        // Ambil balita di posyandu user yang BELUM ada di daftar sudahDinilai
        $balitas = Balita::where('posyandu_id', $user->posyandu_id)
            ->whereNotIn('id', $sudahDinilai)
            ->get(['id', 'nama']);

        // Kembalikan sebagai JSON untuk diolah JavaScript di frontend
        return response()->json($balitas);
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request) //menyimpan penilaian balita baru
    {
        $tanggalPenilaian = $request->input('tanggal_penilaian', now()->toDateString());
        $request->merge(['tanggal_penilaian' => $tanggalPenilaian]);

        // Validasi input dengan pesan error dalam Bahasa Indonesia
        $request->validate([
            'balita_id' => 'required',
            'tanggal_penilaian' => 'nullable|date',
            'penilaian' => 'required|array',
            'penilaian.*' => 'required'
        ], [
            'balita_id.required' => 'Balita wajib dipilih.',
            'tanggal_penilaian.date' => 'Format tanggal penilaian tidak valid.',
            'penilaian.required' => 'Semua kriteria wajib dinilai/diisi.',
            'penilaian.array' => 'Data penilaian harus berupa kriteria.',
            'penilaian.*.required' => 'Semua kriteria wajib dinilai/diisi.'
        ]);

        // Cek apakah balita sudah dinilai pada bulan & tahun yang sama (cegah duplikasi)
        $tanggal = Carbon::parse($tanggalPenilaian);
        $sudahAda = PenilaianBalita::where('balita_id', $request->balita_id)
            ->whereMonth('tanggal_penilaian', $tanggal->month)
            ->whereYear('tanggal_penilaian', $tanggal->year)
            ->exists();

        // Jika sudah ada, tolak dan kembalikan error
        if ($sudahAda) {
            return back()->withErrors([
                'balita_id' => 'Balita ini sudah dinilai pada bulan yang sama.'
            ])->withInput();
        }

        // Simpan penilaian untuk setiap kriteria
        foreach ($request->penilaian as $kriteria_id => $kategori_id) {
            $kriteria = Kriteria::find($kriteria_id);
            $kategori = KategoriPenilaian::find($kategori_id);

            PenilaianBalita::create([
                'balita_id' => $request->balita_id,
                'kriteria_id' => $kriteria_id,
                'kategori_penilaian_id' => $kategori_id,
                'tanggal_penilaian' => $tanggalPenilaian,
                // Simpan snapshot bobot & nilai saat ini agar tidak berubah
                // meskipun bobot/nilai diubah di kemudian hari
                'bobot_snapshot' => $kriteria ? $kriteria->bobot : null,
                'nilai_kategori_snapshot' => $kategori ? $kategori->nilai : null,
            ]);
        }

        return redirect()->route('penilaian_balita.index')
            ->with('success', 'Penilaian berhasil disimpan');
    }

    // =========================
    // EDIT
    // =========================
    public function edit(int $id) // menampilkan form edit balita 
    {
        // Cari penilaian berdasarkan ID
        $penilaian = PenilaianBalita::findOrFail($id);

        // Ambil semua penilaian milik balita yang sama (semua kriterianya)
        $penilaians = PenilaianBalita::where('balita_id', $penilaian->balita_id)->get();

        // Buat mapping kriteria_id => kategori_penilaian_id
        // Digunakan di view untuk menandai opsi yang sudah dipilih sebelumnya 
        $selected = $penilaians->pluck('kategori_penilaian_id', 'kriteria_id');

        // Ambil daftar balita di posyandu user
        $balitas = Balita::where('posyandu_id', Auth::user()->posyandu_id)->get();

        // Ambil semua kriteria beserta kategori penilaiannya
        $kriterias = Kriteria::with('kategoriPenilaians')->get();

        return view('kader.penilaian_balita.edit', compact(
            'penilaian',
            'balitas',
            'kriterias',
            'selected'
        ));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, int $id) //memperbarui penilaian balita yang sudah ada
    {
        $tanggalPenilaian = $request->input('tanggal_penilaian', now()->toDateString());
        $request->merge(['tanggal_penilaian' => $tanggalPenilaian]);

        // Validasi input dengan pesan error dalam Bahasa Indonesia
        $request->validate([
            'balita_id'         => 'required',
            'tanggal_penilaian' => 'nullable|date',
            'penilaian'         => 'required|array',
            'penilaian.*'       => 'required'
        ], [
            'balita_id.required'         => 'Balita wajib dipilih.',
            'tanggal_penilaian.date'     => 'Format tanggal penilaian tidak valid.',
            'penilaian.required'         => 'Semua kriteria wajib dinilai/diisi.',
            'penilaian.array'            => 'Data penilaian harus berupa kriteria.',
            'penilaian.*.required'       => 'Semua kriteria wajib dinilai/diisi.'
        ]);

        // Loop setiap penilaian dan update/buat record per kriteria
        foreach ($request->penilaian as $kriteria_id => $kategori_id) {
            $kriteria = Kriteria::find($kriteria_id);
            $kategori = KategoriPenilaian::find($kategori_id);

            // update atau buat baru jika belum ada record untuk balita_id + kriteria_id
            // Jika ketemu → update, jika tidak → buat baru
            PenilaianBalita::updateOrCreate(
                ['balita_id' => $request->balita_id, 'kriteria_id' => $kriteria_id],
                [
                    'kategori_penilaian_id'   => $kategori_id,
                    'tanggal_penilaian'       => $tanggalPenilaian,
                    // Perbarui snapshot bobot & nilai ke nilai terkini
                    'bobot_snapshot'          => $kriteria ? $kriteria->bobot : null,
                    'nilai_kategori_snapshot' => $kategori ? $kategori->nilai : null,
                ]
            );
        }

        // Ambil bulan & tahun dari tanggal penilaian yang diinput
        $tanggal = Carbon::parse($request->tanggal_penilaian);

        // Redirect ke halaman index dengan filter bulan & tahun yang sesuai
        // agar user langsung melihat data yang baru diubah
        return redirect()->route('penilaian_balita.index', [
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year,
        ])->with('success', 'Penilaian berhasil diperbarui');
    }

    // =========================
    // DESTROY
    // =========================
    public function destroy(Request $request, int $balita_id) //menghapus penilaian balita di bulan tersebut
    {
        $user = Auth::user();

        // Pastikan balita milik posyandu user yang login 
        $balita = Balita::where('id', $balita_id)
            ->where('posyandu_id', $user->posyandu_id)
            ->firstOrFail();

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if ($bulan && $tahun) {
            // Hapus penilaian hanya pada bulan dan tahun tersebut
            PenilaianBalita::where('balita_id', $balita->id)
                ->whereMonth('tanggal_penilaian', $bulan)
                ->whereYear('tanggal_penilaian', $tahun)
                ->delete();
        } else {
            // Fallback: hapus semua jika tidak ada filter
            PenilaianBalita::where('balita_id', $balita->id)->delete();
        }

        return redirect()->route('penilaian_balita.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Penilaian ' . $balita->nama . ' berhasil dihapus.');
    }
}
