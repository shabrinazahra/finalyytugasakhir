<?php

namespace App\Services;

use App\Models\Balita;
use App\Models\Kriteria;
use App\Models\PenilaianBalita;

class MooraCalculationService
{
    /**
     * @param string|null $periode (format: 'Y-m')
     * @param int|null $posyanduId
     * @return array
     */
    public function calculateMoora($periode = null, $posyanduId = null)
    {
        // Ambil balita sesuai posyandu (jika posyandu_id diberikan)
        $balitaQuery = Balita::query();
        if ($posyanduId) {
            $balitaQuery->where('posyandu_id', $posyanduId);
        }
        $allBalitas = $balitaQuery->with('posyandu')->get();
        $balitaIds = $allBalitas->pluck('id');

        // Ambil penilaian pada periode ini
        $penilaianQuery = PenilaianBalita::with(['balita.posyandu', 'kriteria', 'kategori'])
            ->whereIn('balita_id', $balitaIds);

        if ($periode) {
            $penilaianQuery->where('tanggal_penilaian', 'LIKE', $periode . '-%');
        }

        $rawPenilaians = $penilaianQuery->get();

        // Cek kelengkapan penilaian balita di periode ini
        $isComplete = true;
        $incompleteBalitas = [];

        if ($periode) {
            $activeKriterias = Kriteria::orderBy('id')->get();
            $activeKriteriasCount = $activeKriterias->count();
            $penilaiansByBalita = $rawPenilaians->groupBy('balita_id');

            foreach ($allBalitas as $balita) {
                $balitaPenilaians = $penilaiansByBalita->get($balita->id);
                if (!$balitaPenilaians) {
                    $incompleteBalitas[] = $balita;
                } else {
                    $assessedActiveCount = $balitaPenilaians
                        ->whereIn('kriteria_id', $activeKriterias->pluck('id'))
                        ->pluck('kriteria_id')
                        ->unique()
                        ->count();

                    if ($assessedActiveCount < $activeKriteriasCount) {
                        $incompleteBalitas[] = $balita;
                    }
                }
            }
            $isComplete = empty($incompleteBalitas);
        }

        // Cari kriteria yang benar-benar dievaluasi di periode/grup ini
        $assessedKriteriaIds = $rawPenilaians->pluck('kriteria_id')->unique()->toArray();

        // Ambil kriteria yang relevan (termasuk yang mungkin sudah di-soft-delete)
        $kriterias = Kriteria::withTrashed()->whereIn('id', $assessedKriteriaIds)->orderBy('id')->get();
        if ($kriterias->isEmpty()) {
            $kriterias = Kriteria::orderBy('id')->get(); // Fallback
        }

        // hanya tampilkan 
        $kriterias = $kriterias->filter(function ($k) {
            return is_null($k->deleted_at);
        })->values();

        $activeKriteriaIds = $kriterias->pluck('id')->toArray();
        $requiredKriteriaCount = count($activeKriteriaIds);

        // Group berdasarkan balita_id saja (per balita per periode)
        // Gunakan separator yang aman: "::" untuk menghindari konflik underscore pada tanggal/id
        $grouped = $rawPenilaians->groupBy(function ($item) {
            return $item->balita_id . '::' . $item->tanggal_penilaian;
        });

        // 1. Matriks Keputusan (X) & Filter Penilaian Lengkap
        $alternatives   = []; // [group_key] => ['balita' => ..., 'tanggal' => ...]
        $decisionMatrix = []; // [group_key][kriteria_id] = nilai (1,3,5)
        $bobotMatrix    = []; // [group_key][kriteria_id] = bobot snapshot

        foreach ($grouped as $key => $items) {
            // Skip jika penilaian belum lengkap untuk kriteria yang aktif
            $evaluatedActiveIds = $items->pluck('kriteria_id')->intersect($activeKriteriaIds)->unique();
            if ($evaluatedActiveIds->count() < $requiredKriteriaCount) {
                continue;
            }

            $firstItem = $items->first();
            $balita    = $firstItem->balita;
            if (!$balita) continue;

            $alternatives[$key] = [
                'balita'  => $balita,
                'tanggal' => $firstItem->tanggal_penilaian,
            ];

            foreach ($kriterias as $k) {
                $p = $items->where('kriteria_id', $k->id ?? null)->first();

                // Nilai kategori: snapshot > live kategori > default 1
                $nilaiKat = ($p && $p->nilai_kategori_snapshot !== null)
                    ? $p->nilai_kategori_snapshot
                    : ($p && $p->kategori ? $p->kategori->nilai : 1);

                // Bobot: snapshot (bobot saat penilaian disimpan) > bobot live > 0
                // Prioritaskan snapshot agar data lama tidak berubah jika bobot AHP diupdate
                $bobotKriteria = ($p && $p->bobot_snapshot !== null && $p->bobot_snapshot > 0)
                    ? $p->bobot_snapshot
                    : ($k->bobot ?? 0);

                $decisionMatrix[$key][$k->id] = $nilaiKat;
                $bobotMatrix[$key][$k->id]    = $bobotKriteria;
            }
        }

        if (empty($decisionMatrix)) {
            return [
                'kriterias'          => $kriterias,
                'alternatives'       => [],
                'decisionMatrix'     => [],
                'normalizedMatrix'   => [],
                'weightedMatrix'     => [],
                'bobotMatrix'        => [],
                'results'            => [],
                'isComplete'         => $isComplete,
                'incompleteBalitas'  => $incompleteBalitas,
            ];
        }

        // 2. Pembagi Normalisasi: sqrt( Σ xij² ) per kriteria
        $divisors = [];
        foreach ($kriterias as $k) {
            $sumSq = 0;
            foreach ($decisionMatrix as $scores) {
                $sumSq += pow($scores[$k->id], 2);
            }
            $divisors[$k->id] = ($sumSq > 0) ? sqrt($sumSq) : 1;
        }

        // 3. Matriks Ternormalisasi: X* = xij / sqrt(Σxij²)
        $normalizedMatrix = [];
        foreach ($decisionMatrix as $key => $scores) {
            foreach ($kriterias as $k) {
                $normalizedMatrix[$key][$k->id] = $scores[$k->id] / $divisors[$k->id];
            }
        }

        // 4. Matriks Ternormalisasi Terbobot: wij = wj * X*ij
        //    Bobot diambil dari bobotMatrix (snapshot per baris) agar konsisten dengan
        //    data penilaian yang sudah tersimpan.
        $weightedMatrix = [];
        foreach ($normalizedMatrix as $key => $scores) {
            foreach ($kriterias as $k) {
                $bobot = $bobotMatrix[$key][$k->id] ?? ($k->bobot ?? 0); // dari snapshot
                $weightedMatrix[$key][$k->id] = $scores[$k->id] * $bobot;
            }
        }

        // 5. Nilai Optimasi Yi = Σ Benefit − Σ Cost
        $results = [];
        foreach ($weightedMatrix as $key => $scores) {
            $sumBenefit = 0;
            $sumCost    = 0;

            foreach ($kriterias as $k) {
                if ($k->atribut === 'cost') {
                    $sumCost    += $scores[$k->id];
                } else {
                    $sumBenefit += $scores[$k->id];
                }
            }

            $results[] = [
                'key'          => $key,
                'balita'       => $alternatives[$key]['balita'],
                'tanggal'      => $alternatives[$key]['tanggal'],
                'nilai_akhir'  => $sumBenefit - $sumCost,
                'sum_benefit'  => $sumBenefit,
                'sum_cost'     => $sumCost,
            ];
        }

        // Map kode_kriteria ke ID kriteria database untuk mempermudah tie-breaker
        $kriteriaKodeMap = [];
        foreach ($kriterias as $k) {
            $kriteriaKodeMap[$k->kode_kriteria] = $k->id;
        }

        // Urutkan descending Yi (nilai_akhir). Jika Yi sama, lakukan tie-breaker dengan membandingkan
        // kriteria K1, lalu K2, ..., hingga K7 secara berurutan. Jika masih sama, urutkan berdasarkan nama balita.
        usort($results, function ($a, $b) use ($kriteriaKodeMap, $decisionMatrix) {
            // Bandingkan Nilai Akhir (Yi) - Descending
            if (abs($b['nilai_akhir'] - $a['nilai_akhir']) > 0.000001) {
                return $b['nilai_akhir'] <=> $a['nilai_akhir'];
            }

            // Tie-breaker: Bandingkan kriteria K1 sampai K7 berurutan
            for ($i = 1; $i <= 7; $i++) {
                $kode = 'K' . $i;
                if (isset($kriteriaKodeMap[$kode])) {
                    $kId = $kriteriaKodeMap[$kode];

                    // Ambil nilai kategori dari matriks keputusan (skor risiko 5, 3, atau 1)
                    $scoreA = $decisionMatrix[$a['key']][$kId] ?? 0;
                    $scoreB = $decisionMatrix[$b['key']][$kId] ?? 0;

                    if ($scoreB != $scoreA) {
                        return $scoreB <=> $scoreA; // Descending (nilai/risiko lebih besar didahulukan)
                    }
                }
            }

            // Jika semua nilai kriteria sama, urutkan berdasarkan nama secara alfabetis
            return strcmp($a['balita']->nama, $b['balita']->nama);
        });

        // 6. Penentuan Status Prioritas Pareto per Posyandu
        $this->applyParetoThreshold($results);

        // -------------------------------------------------------
        // Re-key matrix dari "balitaId::tanggal" → "balitaId"
        // Menggunakan explode dengan batas 2 agar tanggal yang
        // mengandung karakter apapun tidak terpotong.
        // -------------------------------------------------------
        $rekeyedAlternatives = [];
        foreach ($alternatives as $key => $alt) {
            [$balitaId] = explode('::', $key, 2);
            $rekeyedAlternatives[$balitaId] = $alt['balita'];
        }

        $rekeyedDecisionMatrix = [];
        foreach ($decisionMatrix as $key => $val) {
            [$balitaId] = explode('::', $key, 2);
            $rekeyedDecisionMatrix[$balitaId] = $val;
        }

        $rekeyedNormalizedMatrix = [];
        foreach ($normalizedMatrix as $key => $val) {
            [$balitaId] = explode('::', $key, 2);
            $rekeyedNormalizedMatrix[$balitaId] = $val;
        }

        $rekeyedWeightedMatrix = [];
        foreach ($weightedMatrix as $key => $val) {
            [$balitaId] = explode('::', $key, 2);
            $rekeyedWeightedMatrix[$balitaId] = $val;
        }

        // bobotMatrix juga di-rekey agar bisa ditampilkan di view (bobot per baris)
        $rekeyedBobotMatrix = [];
        foreach ($bobotMatrix as $key => $val) {
            [$balitaId] = explode('::', $key, 2);
            $rekeyedBobotMatrix[$balitaId] = $val;
        }

        return [
            'kriterias'          => $kriterias,
            'alternatives'       => $rekeyedAlternatives,
            'decisionMatrix'     => $rekeyedDecisionMatrix,
            'normalizedMatrix'   => $rekeyedNormalizedMatrix,
            'weightedMatrix'     => $rekeyedWeightedMatrix,
            'bobotMatrix'        => $rekeyedBobotMatrix,  // <-- untuk header view
            'results'            => $results,
            'isComplete'         => $isComplete,
            'incompleteBalitas'  => $incompleteBalitas,
        ];
    }

    /**
     * Terapkan klasifikasi prioritas Metode ABC berdasarkan proporsi Pareto (Tinggi = 20%, Sedang = 30%, Rendah = 50%)
     * secara mandiri per posyandu.
     * 
     * Dokumen Penjelasan untuk Skripsi:
     * 1. Data balita diurutkan terlebih dahulu dari nilai Yi terbesar (prioritas tertinggi/risiko stunting tertinggi) 
     *    ke terkecil menggunakan metode MOORA.
     * 2. Proporsi kuota target untuk masing-masing kategori dihitung secara proporsional per posyandu menggunakan 
     *    pembulatan standar (round). Untuk 48 balita, ini akan menghasilkan:
     *    - Prioritas Tinggi (Tinggi)   
     *    - Prioritas Sedang (Sedang)   
     *    - Prioritas Rendah (Rendah)  
     * 3. Aturan Batas Kategori (Yi Identik):
     *    Jika terdapat beberapa alternatif balita yang memiliki nilai optimasi Yi (nilai_akhir) yang persis sama 
     *    pada batas transisi kategori (misal batas antara Tinggi ke Sedang, atau Sedang ke Rendah), 
     *    alternatif tersebut tidak dipisahkan ke kategori berbeda melainkan disatukan di kategori prioritas yang lebih tinggi.
     */
    private function applyParetoThreshold(array &$results): void
    {
        if (empty($results)) return;

        // Kelompokkan hasil per posyandu
        $groupedByPosyandu = [];
        foreach ($results as $index => $res) {
            $posyanduId = $res['balita']->posyandu_id ?? 0;
            $groupedByPosyandu[$posyanduId][] = [
                'original_index' => $index,
                'res'            => $res,
            ];
        }

        foreach ($groupedByPosyandu as $items) {
            $total = count($items);
            if ($total === 0) continue;

            // Hitung target kuota ideal dengan pembulatan standar (round)
            $highCount   = (int) round($total * 0.20);
            $mediumCount = (int) round($total * 0.30);

            // Pengamanan agar kuota tidak melebihi jumlah total data
            if ($highCount + $mediumCount > $total) {
                $highCount = (int) floor($total * 0.20);
                $mediumCount = (int) floor($total * 0.30);
            }
            $highCount   = max(0, $highCount);
            $mediumCount = max(0, $mediumCount);

            // Dapatkan nilai Yi (nilai_akhir) pembatas di batas Tinggi/Sedang
            $thresholdTinggi = null;
            if ($highCount > 0 && isset($items[$highCount - 1])) {
                $thresholdTinggi = $items[$highCount - 1]['res']['nilai_akhir'];
            }

            // Dapatkan nilai Yi (nilai_akhir) pembatas di batas Sedang/Rendah
            $thresholdSedang = null;
            if (($highCount + $mediumCount) > 0 && isset($items[$highCount + $mediumCount - 1])) {
                $thresholdSedang = $items[$highCount + $mediumCount - 1]['res']['nilai_akhir'];
            }

            // Kelompokan masing-masing balita
            foreach ($items as $i => $item) {
                $originalIndex = $item['original_index'];
                $yi = $item['res']['nilai_akhir'];

                // Tentukan status awal berdasarkan indeks urutan ideal (Metode ABC standar)
                if ($i < $highCount) {
                    $status = 'Prioritas Tinggi (Sangat Berisiko)';
                    $color  = 'red';
                } elseif ($i < $highCount + $mediumCount) {
                    $status = 'Prioritas Sedang (Berisiko)';
                    $color  = 'yellow';
                } else {
                    $status = 'Prioritas Rendah (Normal)';
                    $color  = 'green';
                }

                // Terapkan penyesuaian aturan batas untuk nilai Yi sama/identik:
                // Jika Yi sama dengan nilai batas Tinggi, satukan semua di kategori Tinggi (upgraded)
                if ($thresholdTinggi !== null && abs($yi - $thresholdTinggi) < 0.000001) {
                    $status = 'Prioritas Tinggi (Sangat Berisiko)';
                    $color  = 'red';
                }
                // Jika Yi sama dengan nilai batas Sedang, satukan semua di kategori Sedang (upgraded)
                elseif ($thresholdSedang !== null && abs($yi - $thresholdSedang) < 0.000001) {
                    $status = 'Prioritas Sedang (Berisiko)';
                    $color  = 'yellow';
                }

                // Simpan status final ke hasil
                $results[$originalIndex]['status'] = $status;
                $results[$originalIndex]['color']  = $color;
            }
        }
    }
}
