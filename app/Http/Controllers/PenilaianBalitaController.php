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

    // =========================
    // INPUT MASSAL
    // =========================
    public function createMassal(Request $request)
    {
        $user = Auth::user();
        $tanggalInput = $request->input('tanggal');
        
        try {
            $tanggal = $tanggalInput ? Carbon::parse($tanggalInput) : now();
        } catch (\Exception $e) {
            $tanggal = now();
        }

        // Ambil ID balita yang sudah dinilai pada bulan & tahun dari tanggal terpilih
        $sudahDinilai = PenilaianBalita::whereHas('balita', function ($q) use ($user) {
            $q->where('posyandu_id', $user->posyandu_id);
        })
            ->whereMonth('tanggal_penilaian', $tanggal->month)
            ->whereYear('tanggal_penilaian', $tanggal->year)
            ->pluck('balita_id')
            ->unique()
            ->toArray();

        // Ambil balita yang belum dinilai pada bulan & tahun dari tanggal terpilih
        $balitas = Balita::where('posyandu_id', $user->posyandu_id)
            ->whereNotIn('id', $sudahDinilai)
            ->latest()
            ->get();

        // Ambil kriteria beserta kategori penilaian
        $kriterias = Kriteria::with('kategoriPenilaians')
            ->orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')
            ->get();

        return view('kader.penilaian_balita.create_massal', compact('balitas', 'kriterias', 'tanggal'));
    }

    public function storeMassal(Request $request)
    {
        $request->validate([
            'tanggal_penilaian' => 'required|date',
            'penilaian' => 'required|array',
        ], [
            'tanggal_penilaian.required' => 'Tanggal penilaian wajib diisi.',
            'tanggal_penilaian.date' => 'Format tanggal tidak valid.',
            'penilaian.required' => 'Minimal satu balita harus dinilai.',
        ]);

        $user = Auth::user();
        if (!$user->posyandu_id) {
            return back()->with('error', 'User belum memiliki posyandu');
        }

        $tanggalPenilaian = $request->input('tanggal_penilaian');
        $tanggal = Carbon::parse($tanggalPenilaian);
        $imported = 0;

        // penilaian[balita_id][kriteria_id] = kategori_id
        foreach ($request->input('penilaian', []) as $balitaId => $kriteriaValues) {
            // Pastikan balita milik posyandu user
            $balita = Balita::where('id', $balitaId)
                ->where('posyandu_id', $user->posyandu_id)
                ->first();

            if (!$balita) {
                continue;
            }

            // Cek duplikasi
            $sudahAda = PenilaianBalita::where('balita_id', $balitaId)
                ->whereMonth('tanggal_penilaian', $tanggal->month)
                ->whereYear('tanggal_penilaian', $tanggal->year)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            // Cek apakah semua kriteria terisi
            $allFilled = true;
            foreach ($kriteriaValues as $kategoriId) {
                if (empty($kategoriId)) {
                    $allFilled = false;
                    break;
                }
            }

            if (!$allFilled) {
                continue; // Skip balita yang belum lengkap kriterianya
            }

            foreach ($kriteriaValues as $kriteriaId => $kategoriId) {
                if (empty($kategoriId)) {
                    continue;
                }

                $kriteria = Kriteria::find($kriteriaId);
                $kategori = KategoriPenilaian::find($kategoriId);

                if (!$kriteria || !$kategori) {
                    continue;
                }

                PenilaianBalita::create([
                    'balita_id' => $balitaId,
                    'kriteria_id' => $kriteriaId,
                    'kategori_penilaian_id' => $kategoriId,
                    'tanggal_penilaian' => $tanggalPenilaian,
                    'bobot_snapshot' => $kriteria->bobot,
                    'nilai_kategori_snapshot' => $kategori->nilai,
                ]);
            }

            $imported++;
        }

        if ($imported === 0) {
            return back()->with('error', 'Tidak ada data yang berhasil disimpan. Pastikan semua kriteria terisi.');
        }

        return redirect()->route('penilaian_balita.index', [
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year,
        ])->with('success', "Berhasil menyimpan penilaian untuk $imported balita");
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
