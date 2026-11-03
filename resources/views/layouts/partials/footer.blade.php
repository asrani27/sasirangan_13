<!-- Footer -->
<footer class="bg-slate-900 text-slate-300 pt-16 pb-8 border-t border-slate-800 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
            <!-- Brand description -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        D
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight">DEDIKASI</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Sarana Pemantauan dan Informasi Harga Bahan Kebutuhan Pokok Kota Banjarmasin untuk mendukung stabilitas pangan dan transparansi publik.
                </p>
                <div class="text-xs text-emerald-400 font-medium">
                    Dinas Perdagangan dan Perindustrian Kota Banjarmasin
                </div>
            </div>

            <!-- Main Links -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Navigasi Utama</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('beranda') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Beranda</a></li>
                    <li><a href="{{ route('info-harga') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Info Harga Harian</a></li>
                    <li><a href="{{ route('info-stok') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Info Stok Pangan</a></li>
                    <li><a href="{{ route('grafik.harga') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Grafik Perkembangan</a></li>
                    <li><a href="{{ route('kenaikan') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Sistem EWS Kenaikan</a></li>
                    <li><a href="{{ route('artikel') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-slate-600"></i> Berita &amp; Pengumuman</a></li>
                </ul>
            </div>

            <!-- Contacts -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Kantor &amp; Kontak</h3>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-location-dot text-emerald-500 mt-1"></i>
                        <span>Jl. Brig Jend H. Hasan Basry No. 4, Banjarmasin Utara, Kota Banjarmasin, Kalimantan Selatan</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fas fa-phone text-emerald-500"></i>
                        <span>(0511) 3301234</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fas fa-envelope text-emerald-500"></i>
                        <span>disperindag@banjarmasinkota.go.id</span>
                    </li>
                </ul>
            </div>

            <!-- Officer Login info -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Akses Petugas Pasar</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-4">
                    Aplikasi pencatatan dan verifikasi data harga harian oleh petugas pencatat harga pasar se-Kota Banjarmasin.
                </p>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition">
                    <i class="fas fa-lock text-emerald-400"></i>
                    <span>Login Petugas &bull; Dedikasi</span>
                </a>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <div>
                &copy; {{ date('Y') }} Pemerintah Kota Banjarmasin. Seluruh Hak Cipta Dilindungi Undang-Undang.
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Dedikasi Portal v2.0 (Laravel 12 &amp; Tailwind CSS)</span>
            </div>
        </div>
    </div>
</footer>
