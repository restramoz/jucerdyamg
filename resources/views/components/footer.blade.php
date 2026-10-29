<footer class="bg-[#0A1622] text-white border-t border-white/10" role="contentinfo" aria-label="Informasi Kaki Halaman">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Identity -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-lg bg-[#101820] border border-[#00C8F0]/40 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#00C8F0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                        </svg>
                    </span>
                    <span class="flex flex-col min-w-0">
                        <span class="text-sm font-extrabold text-white truncate">dhanlielektrizitetdenky</span>
                        <span class="text-[11px] text-neutral-400">Pelatihan &amp; Konsultasi K3 Listrik</span>
                    </span>
                </div>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    Katalog 60 layanan pelatihan K3 Listrik: pemahaman bahaya listrik, instalasi dan proteksi, otomasi industri, serta maintenance dan pengukuran.
                </p>
            </div>

            <!-- Navigation -->
            <nav aria-label="Navigasi Kaki Halaman">
                <h3 class="text-xs font-bold text-[#00C8F0] uppercase tracking-wider mb-4">Navigasi</h3>
                <ul class="space-y-2.5 text-sm text-neutral-300" role="list">
                    <li><a href="{{ route('home') }}" class="hover:text-[#00C8F0] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] rounded">Beranda</a></li>
                    <li><a href="{{ route('home') }}#layanan" class="hover:text-[#00C8F0] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] rounded">Layanan</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-[#00C8F0] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] rounded">Artikel</a></li>
                    <li><a href="{{ route('home') }}#keunggulan" class="hover:text-[#00C8F0] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] rounded">Tentang</a></li>
                    <li><a href="{{ route('home') }}#kontak" class="hover:text-[#00C8F0] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C8F0] rounded">Kontak</a></li>
                </ul>
            </nav>

            <!-- Contact -->
            <div>
                <h3 class="text-xs font-bold text-[#00C8F0] uppercase tracking-wider mb-4">Konsultasi</h3>
                <p class="text-sm text-neutral-400 mb-4">Informasi jadwal, persyaratan peserta, dan penyelenggaraan program dikonfirmasi via WhatsApp.</p>
                <x-wa-cta variant="primary" size="sm" label="Konsultasi via WhatsApp" class="bg-[#FFB020] hover:bg-[#E09A00] text-[#061827] hover:text-[#061827] font-bold" />
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <p>&copy; {{ date('Y') }} dhanlielektrizitetdenky. Pelatihan &amp; Konsultasi K3 Listrik.</p>
            <p>Status penyelenggaraan setiap program dikonfirmasi melalui konsultasi sebelum pendaftaran.</p>
        </div>
    </div>
</footer>
