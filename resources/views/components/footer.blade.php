<footer class="bg-industry-black text-white border-t border-dark-mahogany" role="contentinfo" aria-label="Informasi Kaki Halaman">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 sm:py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Col 1: Identity -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-burnt-brick border border-burnt-brick/80 flex items-center justify-center text-white shadow-inner">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m14 13-8.381 8.38a1 1 0 0 1-3.001-3L11 9.999" />
                            <path d="M15.973 4.027A13 13 0 0 0 5.902 2.373c-1.398.342-1.092 2.158.277 2.601a19.9 19.9 0 0 1 5.822 3.024" />
                            <path d="M16.001 11.999a19.9 19.9 0 0 1 3.024 5.824c.444 1.369 2.26 1.676 2.603.278A13 13 0 0 0 20 8.069" />
                            <path d="M18.352 3.352a1.205 1.205 0 0 0-1.704 0l-5.296 5.296a1.205 1.205 0 0 0 0 1.704l2.296 2.296a1.205 1.205 0 0 0 1.704 0l5.296-5.296a1.205 1.205 0 0 0 0-1.704z" />
                        </svg>
                    </div>
                    <span class="text-base font-bold text-white tracking-tight">
                        <span class="text-safety-orange">Zonyberg</span>
                    </span>
                </div>
                <p class="text-sm text-neutral-300 leading-relaxed">
                    Portal informasi pelatihan dan skema kompetensi pertambangan dan energi: rincian program, unit kompetensi, dan layanan konsultasi untuk kebutuhan Pertambangan &amp; Energi.
                </p>
                <div class="pt-1">
                    <x-wa-cta variant="primary" size="sm" label="Konsultasi WhatsApp" class="bg-safety-orange hover:bg-burnt-brick text-industry-black hover:text-white font-bold" />
                </div>
            </div>

            <!-- Col 2: Program & Layanan -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-safety-orange uppercase tracking-wider">
                    Skema Kompetensi
                </h3>
                @php
                    $footerSchemes = array_slice(app(\App\Services\SchemeService::class)->all(), 0, 3, true);
                @endphp
                <ul class="space-y-2 text-xs sm:text-sm text-neutral-200" role="list">
                    @foreach ($footerSchemes as $slug => $scheme)
                        <li>
                            <a href="{{ route('program.show', $slug) }}" class="hover:text-safety-orange transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-safety-orange rounded">
                                {{ $scheme['title'] }}
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('articles.index') }}" class="hover:text-safety-orange transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-safety-orange rounded">
                            Artikel &amp; Panduan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="mt-12 pt-8 border-t border-dark-mahogany/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-400">
            <p>
                &copy; {{ date('Y') }} Zonyberg. Seluruh hak cipta dilindungi.
            </p>
            <p class="text-neutral-400">
                Acuan materi pembahasan bersifat umum; status penyelenggaraan menunggu verifikasi. Peta wilayah menampilkan konteks administratif jangkauan layanan.
            </p>
        </div>
    </div>
</footer>
