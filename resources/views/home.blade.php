@extends('layouts.app')

@section('title', 'dhanlielektrizitetdenky — Pelatihan & Konsultasi K3 Listrik')

@section('meta_description', 'Katalog 60 layanan pelatihan K3 Listrik dan konsultasi keselamatan kelistrikan: instalasi, proteksi, otomasi industri, serta maintenance dan pengukuran.')

@section('content')
@php
    $regionalService = resolve(App\Services\RegionalService::class);
    $provinces = $regionalService->getAllProvinces();

    $groups = app(\App\Services\SchemeService::class)->groupedForPresentation();

    $groupMeta = [
        'k3-listrik' => [
            'title' => 'K3 Listrik',
            'desc' => 'Dasar keselamatan kelistrikan: bahaya listrik, arc flash, APD, dan penanganan kecelakaan listrik.',
            'image' => '/images/home/panel-k3-switchgear.jpg',
            'alt' => 'Panel switchgear industri dengan tanda bahaya tegangan listrik berbahaya',
        ],
        'instalasi-proteksi' => [
            'title' => 'Instalasi & Proteksi',
            'desc' => 'Instalasi listrik tenaga, grounding, proteksi beban lebih, hingga switchgear dan transformator.',
            'image' => '/images/home/panel-instalasi-listrik.jpg',
            'alt' => 'Teknisi listrik memasang instalasi penerangan pada bangunan',
        ],
        'otomasi-industri' => [
            'title' => 'Otomasi Industri',
            'desc' => 'PLC, SCADA, elektronika industri, dan sistem kontrol otomasi pada fasilitas produksi.',
            'image' => '/images/home/panel-otomasi-industri.jpg',
            'alt' => 'Insinyur otomasi industri bekerja dengan laptop di sel produksi',
        ],
        'maintenance-pengukuran' => [
            'title' => 'Maintenance & Pengukuran',
            'desc' => 'Troubleshooting kelistrikan, testing dan commissioning, serta penggunaan alat ukur listrik.',
            'image' => '/images/home/panel-pengukuran-multimeter.jpg',
            'alt' => 'Pengukuran rangkaian elektronika menggunakan multimeter',
        ],
    ];

    $groupIcons = [
        'k3-listrik' => 'M13 2L3 14h9l-1 8 10-12h-9l1-8z',
        'instalasi-proteksi' => 'M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z',
        'otomasi-industri' => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33h0a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51h0a1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82v0a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z',
        'maintenance-pengukuran' => 'M12 3v3m0 12v3M3 12h3m12 0h3M6.3 6.3l2.1 2.1m7.2 7.2l2.1 2.1M6.3 17.7l2.1-2.1m7.2-7.2l2.1-2.1M14 12a2 2 0 11-4 0 2 2 0 014 0z',
    ];

    $latestArticles = \App\Models\Article::published()
        ->orderBy('published_at', 'desc')
        ->take(4)
        ->get();
@endphp

{{-- A. HERO --}}
<section id="home"
         class="relative overflow-hidden bg-[#061827] text-white"
         aria-labelledby="hero-heading">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12">

            <div class="flex flex-col justify-center py-10 sm:py-16 lg:py-24">

                <span class="inline-flex self-start items-center gap-2 rounded-full border border-[#00C8F0]/60 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-[#00C8F0] sm:text-sm">
                    <svg class="h-4 w-4"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                    </svg>
                    Pelatihan &amp; Konsultasi K3 Listrik
                </span>

                <h1 id="hero-heading"
                    class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:mt-6 sm:text-5xl xl:text-6xl">
                    Meningkatkan Kompetensi,
                    <span class="text-[#00C8F0]">
                        Menjaga Keselamatan&nbsp;Kerja
                    </span>
                </h1>

                <p class="mt-5 max-w-xl text-sm leading-relaxed text-slate-300 sm:mt-6 sm:text-lg">
                    Program pelatihan K3 Listrik untuk teknisi, operator, dan profesional industri — dari
                    pemahaman bahaya listrik, instalasi dan proteksi, otomasi industri, hingga maintenance
                    serta pengukuran listrik secara aman dan benar.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:mt-8 sm:flex-row sm:gap-4">
                    <x-wa-cta
                        variant="primary"
                        size="lg"
                        label="Konsultasi Program via WhatsApp"
                        class="w-full bg-[#FFB020] font-bold text-[#061827] shadow-lg hover:bg-[#E09A00] hover:text-[#061827] sm:w-auto"
                    />

                    <a href="#layanan"
                       class="inline-flex min-h-[48px] w-full items-center justify-center rounded-lg border-2 border-white/40 px-6 py-3 text-center text-sm font-bold text-white transition-colors hover:border-[#00C8F0] hover:text-[#00C8F0] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] focus-visible:ring-offset-2 focus-visible:ring-offset-[#061827] sm:w-auto sm:px-7 sm:text-base">
                        Lihat Semua Layanan
                    </a>
                </div>

                <div class="mt-7 grid grid-cols-2 gap-3 sm:mt-9 sm:gap-4">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-3 sm:p-4">
                        <p class="text-xl font-extrabold text-[#00C8F0] sm:text-2xl">60</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-300 sm:text-sm">
                            Topik dan layanan pelatihan
                        </p>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-3 sm:p-4">
                        <p class="text-xl font-extrabold text-[#00C8F0] sm:text-2xl">4</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-300 sm:text-sm">
                            Kelompok bidang kelistrikan
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative min-w-0 pb-8 lg:pb-0">
                <div class="relative h-[260px] overflow-hidden rounded-2xl border border-white/10 shadow-2xl sm:h-[380px] lg:h-[540px]">

                    <img src="/images/home/hero-electrician-panel.jpg"
                         alt="Teknisi listrik menggunakan APD saat bekerja pada panel listrik"
                         width="1600"
                         height="1200"
                         loading="eager"
                         fetchpriority="high"
                         decoding="async"
                         onerror="this.onerror=null;this.src='/images/home/panel-otomasi-industri.jpg';"
                         class="absolute inset-0 h-full w-full object-cover object-center" />

                    <div class="absolute inset-0 bg-gradient-to-t from-[#061827]/90 via-[#061827]/10 to-transparent"
                         aria-hidden="true"></div>

                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7">
                        <span class="inline-flex rounded-full border border-[#00C8F0]/40 bg-[#061827]/80 px-3 py-1 text-xs font-bold text-[#00C8F0] backdrop-blur-sm">
                            Keselamatan Kelistrikan
                        </span>

                        <h2 class="mt-3 max-w-md text-xl font-extrabold leading-tight text-white sm:text-2xl lg:text-3xl">
                            Kompetensi Listrik Dimulai dari Keselamatan
                        </h2>

                        <p class="mt-2 max-w-md text-sm leading-relaxed text-slate-200">
                            Pelajari prinsip kerja aman, instalasi, proteksi, dan pemeliharaan sistem kelistrikan.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- B. KATEGORI LAYANAN --}}
<section id="layanan"
         class="bg-[#F4F7FA] py-12 sm:py-16 lg:py-20"
         aria-labelledby="layanan-heading">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-3xl text-center lg:mx-0 lg:text-left">
            <span class="text-xs font-bold uppercase tracking-widest text-[#0F7FA8] sm:text-sm">
                Layanan Kami
            </span>

            <h2 id="layanan-heading"
                class="mt-2 text-2xl font-extrabold tracking-tight text-[#101820] sm:text-3xl lg:text-4xl">
                Program Pelatihan
                <span class="text-[#0F9BC4]">K3 Listrik</span>
            </h2>

            <p class="mt-4 text-sm leading-relaxed text-[#344054] sm:text-base">
                Jelajahi katalog pelatihan kelistrikan yang dikelompokkan berdasarkan bidang kompetensi.
                Setiap layanan memiliki halaman rincian dan dapat dikonsultasikan melalui WhatsApp.
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 items-start gap-5 md:grid-cols-2 lg:mt-10 lg:gap-6">

            @foreach (['k3-listrik', 'instalasi-proteksi', 'otomasi-industri', 'maintenance-pengukuran'] as $groupKey)
                @php
                    $meta = $groupMeta[$groupKey];
                    $iconPath = $groupIcons[$groupKey];
                    $items = $groups[$groupKey] ?? [];
                    $total = count($items);
                @endphp

                <article data-show-group
                         class="group min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-lg">

                    <div class="relative h-40 overflow-hidden sm:h-48 lg:h-56">
                        <img src="{{ $meta['image'] }}"
                             alt="{{ $meta['alt'] }}"
                             width="900"
                             height="700"
                             loading="lazy"
                             decoding="async"
                             onerror="this.onerror=null;this.src='/images/home/hero-electrician-panel.jpg';"
                             class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]" />

                        <div class="absolute inset-0 bg-gradient-to-t from-[#061827]/65 via-transparent to-transparent"
                             aria-hidden="true"></div>

                        <span class="absolute right-3 top-3 inline-flex max-w-[90%] items-center rounded-lg bg-[#061827]/90 px-3 py-1.5 text-xs font-bold text-[#00C8F0] shadow-sm backdrop-blur-sm">
                            {{ $meta['title'] }} · {{ $total }} layanan
                        </span>
                    </div>

                    <div class="flex flex-col p-4 sm:p-6">

                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#061827] text-[#00C8F0] sm:h-11 sm:w-11">
                                <svg class="h-5 w-5"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="{{ $iconPath }}" />
                                </svg>
                            </span>

                            <h3 class="text-lg font-extrabold leading-snug tracking-tight text-[#101820] sm:text-xl">
                                {{ $meta['title'] }}
                            </h3>
                        </div>

                        <p class="mt-3 text-sm leading-relaxed text-[#475467]">
                            {{ $meta['desc'] }}
                        </p>

                        <ul id="layanan-list-{{ $groupKey }}"
                            class="mt-5 grid grid-cols-1 gap-3"
                            role="list">

                            @foreach ($items as $slug => $item)
                                <li class="min-w-0 {{ $loop->index >= 4 ? 'scheme-extra' : '' }}"
                                    @if ($loop->index >= 4) hidden @endif>

                                    <div class="flex h-full flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 transition-all hover:border-[#0F9BC4] hover:bg-white sm:flex-row sm:items-center sm:justify-between">

                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('program.show', $slug) }}"
                                               class="block text-sm font-bold leading-relaxed text-[#101820] transition-colors hover:text-[#0F9BC4] focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] sm:text-base">
                                                {{ $item['title'] }}
                                            </a>

                                            <a href="{{ route('program.show', $slug) }}"
                                               class="mt-2 inline-flex min-h-[24px] items-center text-sm font-bold text-[#0F9BC4] transition-colors hover:text-[#061827] focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0]">
                                                Lihat Detail
                                                <span class="ml-1" aria-hidden="true">→</span>
                                            </a>
                                        </div>

                                        <div class="shrink-0">
                                            <x-wa-cta
                                                variant="wa"
                                                size="sm"
                                                :message="$item['wa_message']"
                                                label="Konsultasi"
                                                class="w-full bg-[#FFB020] px-4 py-2 text-sm font-bold text-[#061827] hover:bg-[#E09A00] hover:text-[#061827] sm:w-auto"
                                            />
                                        </div>

                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        @if ($total > 4)
                            <button type="button"
                                    class="js-show-all mt-5 inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl border-2 border-[#00C8F0] px-5 py-3 text-sm font-bold text-[#0F7FA8] transition-colors hover:bg-[#00C8F0] hover:text-[#061827] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] focus-visible:ring-offset-2 sm:w-auto sm:self-start"
                                    data-all-label="Lihat Semua {{ $total }} Layanan"
                                    data-less-label="Sembunyikan"
                                    aria-expanded="false"
                                    aria-controls="layanan-list-{{ $groupKey }}">
                                Lihat Semua {{ $total }} Layanan
                            </button>
                        @endif

                    </div>
                </article>
            @endforeach

        </div>
    </div>
</section>

{{-- C. KEUNGGULAN --}}
<section id="keunggulan"
         class="overflow-hidden bg-[#101820] text-white"
         aria-labelledby="keunggulan-heading">

    <div class="grid grid-cols-1 lg:grid-cols-2">

        <div class="relative min-h-[260px] overflow-hidden sm:min-h-[340px] lg:min-h-[600px]">
            <img src="/images/home/feature-lineworker-ppe.jpg"
                 alt="Teknisi listrik menggunakan perlengkapan keselamatan saat bekerja pada jaringan listrik"
                 width="1200"
                 height="1600"
                 loading="lazy"
                 decoding="async"
                 onerror="this.onerror=null;this.src='/images/home/hero-electrician-panel.jpg';"
                 class="absolute inset-0 h-full w-full object-cover object-center" />

            <div class="absolute inset-0 bg-gradient-to-t from-[#101820]/80 via-transparent to-transparent lg:bg-gradient-to-r lg:from-transparent lg:to-[#101820]"
                 aria-hidden="true"></div>

            <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 lg:hidden">
                <span class="text-xs font-bold uppercase tracking-widest text-[#00C8F0]">
                    Keselamatan Kerja
                </span>
                <h3 class="mt-2 max-w-md text-xl font-extrabold sm:text-2xl">
                    Bekerja Aman, Kompetensi Terjaga
                </h3>
            </div>
        </div>

        <div class="relative flex flex-col justify-center px-4 py-12 sm:px-6 sm:py-16 lg:px-12 lg:py-20 xl:px-16">

            <span class="text-xs font-bold uppercase tracking-widest text-[#00C8F0] sm:text-sm">
                Kenapa Memilih Kami
            </span>

            <h2 id="keunggulan-heading"
                class="mt-3 max-w-xl text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl">
                Pelatihan yang Berorientasi pada Keselamatan Kerja
            </h2>

            <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-300 sm:text-base">
                Layanan K3 Listrik kami tersusun sebagai katalog pembelajaran kompetensi dengan penekanan
                pada bahaya listrik, prosedur kerja aman, dan praktik lapangan. Rincian setiap program
                bisa dikonsultasikan langsung.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-5 sm:mt-10 sm:grid-cols-2 sm:gap-y-7">

                @foreach ([
                    ['M13 2L3 14h9l-1 8 10-12h-9l1-8z', 'Instruktur Berpengalaman'],
                    ['M4 6h16M4 10h16M4 14h10M4 18h6', 'Materi Terstruktur dan Aplikatif'],
                    ['M8 3v18M16 3v18M3 8h18M3 16h18', 'Metode Pembelajaran Interaktif'],
                    ['M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4zM9 12l2 2 4-4', 'Fokus pada Keselamatan Kerja'],
                ] as $feature)

                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5">
                            <svg class="h-6 w-6 text-[#00C8F0]"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 aria-hidden="true">
                                <path d="{{ $feature[0] }}" />
                            </svg>
                        </span>

                        <p class="pt-2 text-sm font-semibold leading-relaxed text-white sm:text-base">
                            {{ $feature[1] }}
                        </p>
                    </div>

                @endforeach
            </div>

            <div class="mt-8 sm:mt-10">
                <a href="#layanan"
                   class="inline-flex min-h-[46px] items-center justify-center rounded-xl border border-[#00C8F0]/60 px-5 py-3 text-sm font-bold text-[#00C8F0] transition-colors hover:bg-[#00C8F0] hover:text-[#061827] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0]">
                    Jelajahi Program Pelatihan
                    <span class="ml-2" aria-hidden="true">→</span>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- D. DIREKTORI WILAYAH --}}
<section id="wilayah"
         class="border-b border-slate-200 bg-white py-12 sm:py-14"
         aria-labelledby="wilayah-heading">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">

            <div class="max-w-2xl">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0F7FA8]">
                    Wilayah
                </span>

                <h2 id="wilayah-heading"
                    class="mt-2 text-xl font-extrabold tracking-tight text-[#101820] sm:text-2xl">
                    Layanan K3 Listrik di {{ count($provinces) }} Provinsi
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-[#475467] sm:text-base">
                    Jelajahi halaman wilayah provinsi dan kota/kabupaten sebagai informasi cakupan layanan.
                </p>
            </div>

        </div>

        <div id="wilayah-grid"
             data-show-group
             class="mt-6 grid grid-cols-2 gap-2.5 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-6">

            @foreach ($provinces as $prov)
                <a href="{{ route('regional.province', $prov['slug']) }}"
                   class="rounded-xl border border-slate-200 bg-[#F4F7FA] px-3 py-3 transition-colors hover:border-[#00C8F0] hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] {{ $loop->index >= 6 ? 'scheme-extra' : '' }}"
                   @if ($loop->index >= 6) hidden @endif>

                    <span class="block truncate text-xs font-semibold text-[#101820] sm:text-sm">
                        {{ $prov['name'] }}
                    </span>
                </a>
            @endforeach

        </div>

        @if (count($provinces) > 6)
            <button type="button"
                    class="js-show-all mt-5 inline-flex min-h-[44px] w-full items-center justify-center rounded-xl border-2 border-[#00C8F0] px-5 py-3 text-sm font-bold text-[#0F7FA8] transition-colors hover:bg-[#00C8F0] hover:text-[#061827] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] focus-visible:ring-offset-2 sm:w-auto"
                    data-all-label="Lihat Semua {{ count($provinces) }} Provinsi"
                    data-less-label="Sembunyikan"
                    aria-expanded="false"
                    aria-controls="wilayah-grid">
                Lihat Semua {{ count($provinces) }} Provinsi
            </button>
        @endif

    </div>
</section>

{{-- E. ARTIKEL TERBARU --}}
<section id="artikel"
         class="bg-[#F4F7FA] py-12 sm:py-16 lg:py-20"
         aria-labelledby="artikel-heading">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

            <div class="max-w-2xl">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0F7FA8] sm:text-sm">
                    Artikel Terbaru
                </span>

                <h2 id="artikel-heading"
                    class="mt-2 text-2xl font-extrabold tracking-tight text-[#101820] sm:text-3xl lg:text-4xl">
                    Informasi &amp; Tips
                    <span class="text-[#0F9BC4]">K3 Listrik</span>
                </h2>

                <p class="mt-4 text-sm leading-relaxed text-[#475467] sm:text-base">
                    Artikel mengenai keselamatan kerja listrik, teknologi kelistrikan, dan panduan praktis
                    untuk meningkatkan pemahaman keselamatan kerja.
                </p>
            </div>

            <a href="{{ route('articles.index') }}"
               class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl border-2 border-[#00C8F0] px-6 py-3 text-sm font-bold text-[#0F7FA8] transition-colors hover:bg-[#00C8F0] hover:text-[#061827] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] focus-visible:ring-offset-2 sm:w-auto">
                Lihat Semua Artikel
                <span class="ml-2" aria-hidden="true">→</span>
            </a>

        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:mt-10 lg:grid-cols-3 lg:gap-6">

            @forelse ($latestArticles as $article)
                <article class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-lg">

                    <a href="{{ route('articles.show', $article->slug) }}"
                       class="block overflow-hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0]"
                       aria-label="{{ $article->title }}">

                        <div class="aspect-[16/10] overflow-hidden bg-slate-200">

                            @if ($article->featured_image)
                                <img src="{{ asset('storage/' . $article->featured_image) }}"
                                     alt="Ilustrasi artikel: {{ $article->title }}"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null;this.src='/images/home/hero-electrician-panel.jpg';"
                                     class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.03]" />
                            @else
                                <img src="/images/home/panel-k3-switchgear.jpg"
                                     alt="Panel listrik industri bertanda tegangan berbahaya"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null;this.src='/images/home/hero-electrician-panel.jpg';"
                                     class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.03]" />
                            @endif

                        </div>
                    </a>

                    <div class="flex flex-1 flex-col p-5">

                        <span class="self-start rounded-full bg-[#00C8F0]/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-[#0F7FA8]">
                            K3 Listrik
                        </span>

                        <h3 class="mt-3 text-base font-bold leading-snug text-[#101820] sm:text-lg">
                            <a href="{{ route('articles.show', $article->slug) }}"
                               class="transition-colors hover:text-[#0F9BC4] focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0]">
                                {{ \Illuminate\Support\Str::limit($article->title, 68) }}
                            </a>
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-[#475467]">
                            {{ \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->content), 120) }}
                        </p>

                        <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-4">

                            @if ($article->published_at)
                                <time datetime="{{ $article->published_at->toDateString() }}"
                                      class="text-xs text-slate-500">
                                    {{ $article->published_at->translatedFormat('d F Y') }}
                                </time>
                            @else
                                <span></span>
                            @endif

                            <a href="{{ route('articles.show', $article->slug) }}"
                               class="inline-flex shrink-0 items-center text-sm font-bold text-[#0F7FA8] hover:text-[#061827]">
                                Baca Artikel
                                <span class="ml-1" aria-hidden="true">→</span>
                            </a>

                        </div>
                    </div>
                </article>

            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:col-span-2 lg:col-span-3">
                    <p class="text-sm leading-relaxed text-[#475467]">
                        Belum ada artikel terpublikasi. Artikel mengenai K3 Listrik akan ditampilkan
                        di bagian ini setelah tersedia.
                    </p>
                </div>
            @endforelse

        </div>
    </div>
</section>

{{-- F. BANNER KONSULTASI --}}
<section id="kontak"
         class="relative overflow-hidden bg-[#061827] text-white"
         aria-labelledby="kontak-heading">

    <img src="/images/home/banner-transmisi.jpg"
         alt="Menara transmisi listrik tegangan tinggi"
         width="1920"
         height="1280"
         loading="lazy"
         decoding="async"
         onerror="this.onerror=null;this.removeAttribute('src');"
         class="absolute inset-0 h-full w-full object-cover object-center opacity-35" />

    <div class="absolute inset-0 bg-gradient-to-b from-[#061827]/90 via-[#061827]/75 to-[#061827]/95"
         aria-hidden="true"></div>

    <div class="relative mx-auto max-w-4xl px-4 py-14 text-center sm:px-6 sm:py-20 lg:px-8">

        <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-[#00C8F0]/30 bg-white/5 text-[#FFB020]">

            <svg class="h-7 w-7"
                 viewBox="0 0 24 24"
                 fill="currentColor"
                 aria-hidden="true">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
            </svg>

        </span>

        <h2 id="kontak-heading"
            class="mt-5 text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl">
            Siap Meningkatkan Kompetensi
            <span class="text-[#00C8F0]">K3 Listrik</span> Anda?
        </h2>

        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base lg:text-lg">
            Konsultasikan kebutuhan pelatihan K3 Listrik untuk tim Anda. Pilih layanan yang sesuai,
            tanyakan informasi program, dan diskusikan kebutuhan pelatihan melalui WhatsApp.
        </p>

        <div class="mt-7 flex justify-center sm:mt-8">
            <x-wa-cta
                variant="primary"
                size="lg"
                label="Konsultasi via WhatsApp"
                class="w-full bg-[#FFB020] font-bold text-[#061827] shadow-lg hover:bg-[#E09A00] hover:text-[#061827] sm:w-auto"
            />
        </div>

    </div>
</section>
@endsection
