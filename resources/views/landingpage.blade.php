<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png"
        href="{{ asset('storage/images/logo.png') }}">

    @vite('resources/css/app.css')

    <title>SPK Stunting</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="font-[Poppins] bg-slate-100 text-slate-800 overflow-x-hidden">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-[#163b5b]/95 backdrop-blur-md border-b border-white/10">

        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            <div class="flex items-center justify-between h-20">

                <!-- LOGO -->
                <div class="flex items-center gap-4">

                    <div
                        class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shadow-lg overflow-hidden">

                        <img src="{{ asset('storage/images/logo.png') }}"
                            alt="Logo Posyandu"
                            class="w-10 h-10 object-contain">

                    </div>

                    <div class="text-white">
                        <h1 class="font-semibold text-base">
                            Sistem Pendukung Keputusan
                        </h1>

                        <p class="text-sm text-white/60">
                            Prioritas Penanganan Balita Berisiko Stunting
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </nav>

    <!-- HERO -->
    <section class="min-h-[78vh] pt-28 pb-12 bg-[#163b5b] flex items-center relative overflow-hidden">

        <!-- BACKGROUND -->
        <div class="absolute inset-0">

            <div
                class="absolute top-0 right-0 w-[450px] h-[450px] bg-blue-400/10 rounded-full blur-3xl">
            </div>

            <div
                class="absolute bottom-0 left-0 w-[350px] h-[350px] bg-cyan-300/10 rounded-full blur-3xl">
            </div>

        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-10 w-full relative z-10">

            <div class="flex items-center justify-center h-full text-center">

                <div class="max-w-4xl mx-auto">

                    <!-- TITLE -->
                    <h1 class="max-w-5xl mx-auto text-2xl md:text-3xl lg:text-4xl font-bold text-white leading-snug">
                        Sistem Pendukung Keputusan
                        <span class="text-[#f3c544] block mt-4">
                            Prioritas Penanganan Balita Berisiko Stunting
                        </span>
                    </h1>

                    <!-- DESC -->
                    <p
                        class="mt-6 text-white/70 text-[15px] md:text-base leading-8 max-w-2xl mx-auto">

                        Membantu kader posyandu dan tenaga kesehatan
                        menentukan prioritas penanganan balita berisiko
                        stunting secara cepat, objektif, dan terstruktur
                        menggunakan metode pendukung keputusan.

                    </p>

                    <!-- BUTTON -->
                    <div class="flex flex-wrap justify-center gap-3 mt-8">

                        <a href="/login"
                            class="bg-[#245173] hover:bg-[#1c4463] transition px-6 py-3 rounded-xl text-white font-medium shadow-lg text-sm">

                            Login

                        </a>

                        <button
                            onclick="document.getElementById('tentang').scrollIntoView({behavior:'smooth'})"
                            class="border border-white/20 hover:bg-white/10 transition px-6 py-3 rounded-xl text-white text-sm">

                            Tentang Sistem

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- TENTANG -->
    <!-- TENTANG -->
    <section id="tentang" class="py-20 bg-white">

        <div class="max-w-6xl mx-auto px-6 lg:px-10">

            <!-- TITLE -->
            <div class="mb-10">

                <span
                    class="text-[#245173] font-semibold uppercase tracking-[4px] text-sm">

                    Tentang Sistem

                </span>

                <h2
                    class="text-2xl md:text-3xl font-semibold text-slate-800 mt-4 leading-tight">

                    Mengapa
                    <span class="text-[#245173]">
                        Deteksi Dini Stunting
                    </span>
                    Itu Penting?

                </h2>

            </div>

            <!-- CONTENT -->
            <div class="space-y-6">

                <p class="text-slate-600 text-base leading-8">

                    Stunting adalah kondisi gagal tumbuh pada balita akibat
                    kekurangan gizi kronis yang ditandai dengan tinggi badan
                    di bawah standar usia. Kondisi ini berdampak jangka panjang
                    pada kecerdasan, produktivitas, dan kualitas hidup anak.

                </p>

                <p class="text-slate-600 text-base leading-8">

                    Kader posyandu berada di garis terdepan pemantauan tumbuh
                    kembang balita, namun seringkali kekurangan alat bantu
                    sistematis untuk menentukan tingkat risiko dan urutan
                    prioritas penanganan yang tepat.

                </p>

                <!-- ALERT -->
                <div
                    class="bg-amber-50 border-l-4 border-amber-400 rounded-r-2xl p-5">

                    <p class="text-amber-700 text-base leading-8">

                        ⚠️ Tanpa sistem yang terstruktur, keputusan penanganan
                        bergantung pada intuisi kader sehingga balita berisiko
                        tinggi dapat tidak memperoleh perhatian yang cukup.

                    </p>

                </div>

                <p class="text-slate-600 text-base leading-8">

                    Sistem ini hadir sebagai solusi berbasis teknologi yang
                    membantu pengambilan keputusan secara sistematis,
                    objektif, dan terstandarisasi menggunakan metode
                    pendukung keputusan.

                </p>

            </div>

        </div>

    </section>

    <!-- FITUR -->
    <section id="fitur" class="py-16 bg-slate-50">

        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            <div class="text-center mb-10">

                <span
                    class="text-[#245173] font-semibold uppercase tracking-[4px] text-sm">

                    Fitur Sistem

                </span>

                <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 mt-4">

                    Fitur Utama

                </h2>

            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- CARD -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-lg transition border border-slate-100">

                    <div
                        class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">

                        <x-lucide-baby class="w-7 h-7 text-[#245173]" />

                    </div>

                    <h3 class="font-semibold text-lg mb-3">
                        Data Balita
                    </h3>

                    <p class="text-slate-500 leading-7 text-sm">
                        Pengelolaan data balita .
                    </p>

                </div>

                <!-- CARD -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-lg transition border border-slate-100">

                    <div
                        class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center mb-4">

                        <x-lucide-clipboard-list
                            class="w-7 h-7 text-yellow-600" />

                    </div>

                    <h3 class="font-semibold text-lg mb-3">
                        Penilaian
                    </h3>

                    <p class="text-slate-500 leading-7 text-sm">
                        Input penilaian berdasarkan kriteria dan kategori tertentu.
                    </p>

                </div>

                <!-- CARD -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-lg transition border border-slate-100">

                    <div
                        class="w-14 h-14 rounded-2xl bg-cyan-50 flex items-center justify-center mb-4">

                        <x-lucide-chart-column
                            class="w-7 h-7 text-cyan-600" />

                    </div>

                    <h3 class="font-semibold text-lg mb-3">
                        Perhitungan AHP & MOORA
                    </h3>

                    <p class="text-slate-500 leading-7 text-sm">
                        Proses perhitungan otomatis dan akurat.
                    </p>

                </div>

                <!-- CARD -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-lg transition border border-slate-100">

                    <div
                        class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mb-4">

                        <x-lucide-trophy class="w-7 h-7 text-red-500" />

                    </div>

                    <h3 class="font-semibold text-lg mb-3">
                        Perangkingan
                    </h3>

                    <p class="text-slate-500 leading-7 text-sm">
                        Menampilkan hasil pemeringkatan prioritas penanganan balita berisiko stunting.
                    </p>

                </div>

            </div>

        </div>

    </section>

    <!-- CTA -->
    <section id="kontak"
        class="py-24 bg-[#163b5b] text-center relative overflow-hidden">

        <div class="max-w-4xl mx-auto px-6 relative z-10">

            <h2
                class="text-2xl md:text-3xl font-semibold text-white leading-tight">

                Mari Tingkatkan Pelayanan Posyandu
                dengan Sistem Digital Modern

            </h2>

            <p
                class="text-white/70 mt-6 text-sm md:text-base leading-8 max-w-2xl mx-auto">

                Gunakan sistem pendukung keputusan untuk membantu menentukan
                penanganan balita berisiko stunting secara lebih efektif,
                cepat, dan akurat.

            </p>

            <a href="/login"
                class="inline-block mt-8 bg-[#245173] hover:bg-[#1c4463] transition px-7 py-3 rounded-xl text-white font-medium shadow-lg text-sm">

                Login Sekarang

            </a>

        </div>

    </section>

</body>

</html>