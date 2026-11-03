<?php

use App\Http\Controllers\AduanController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\EWSController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\GantiPassController;
use App\Http\Controllers\HargaAcuanController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PasarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public & Frontend Routes with visitor traffic tracking
Route::middleware('visit')->group(function () {
    Route::get('/', [FrontEndController::class, 'beranda'])->name('beranda');
    Route::get('/info-harga', [FrontEndController::class, 'info_harga'])->name('info-harga');
    Route::match(['get', 'post'], '/info-harga/search', [FrontEndController::class, 'info_harga_search'])->name('info-harga.search');
    Route::get('/info-stok', [FrontEndController::class, 'info_stok'])->name('info-stok');
    Route::match(['get', 'post'], '/info-stok/search', [FrontEndController::class, 'info_stok_search'])->name('info-stok.search');
    Route::get('/grafik', [FrontEndController::class, 'grafik_harga'])->name('grafik');
    Route::get('/grafik/harga', [FrontEndController::class, 'grafik_harga'])->name('grafik.harga');
    Route::match(['get', 'post'], '/grafik/harga/search', [FrontEndController::class, 'grafik_harga_search'])->name('grafik.harga.search');
    Route::get('/grafik/stok', [FrontEndController::class, 'grafik_stok'])->name('grafik.stok');
    Route::match(['get', 'post'], '/grafik/stok/search', [FrontEndController::class, 'grafik_stok_search'])->name('grafik.stok.search');
    Route::get('/artikel', [FrontEndController::class, 'artikel'])->name('artikel');
    Route::get('/artikel/{id}', [FrontEndController::class, 'detailArtikel'])->name('artikel.detail');
    Route::get('/kenaikan', [FrontEndController::class, 'kenaikan'])->name('kenaikan');
});

// Authentication Routes
Route::get('/login', [FrontEndController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout']);

// Admin & Superadmin Protected Routes
Route::middleware(['auth', 'role:superadmin|admin'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Informasi Kategori
    Route::prefix('informasi/kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('kategori');
        Route::get('/add', [KategoriController::class, 'create']);
        Route::post('/add', [KategoriController::class, 'store']);
        Route::get('/edit/{id}', [KategoriController::class, 'edit']);
        Route::post('/edit/{id}', [KategoriController::class, 'update']);
        Route::get('/delete/{id}', [KategoriController::class, 'delete']);
    });

    // Informasi Berita
    Route::prefix('informasi/berita')->group(function () {
        Route::get('/', [BeritaController::class, 'index'])->name('berita');
        Route::get('/add', [BeritaController::class, 'create']);
        Route::post('/add', [BeritaController::class, 'store']);
        Route::get('/edit/{id}', [BeritaController::class, 'edit']);
        Route::post('/edit/{id}', [BeritaController::class, 'update']);
        Route::get('/delete/{id}', [BeritaController::class, 'delete']);
    });

    // Informasi Slider
    Route::prefix('informasi/slider')->group(function () {
        Route::get('/', [SliderController::class, 'index'])->name('slider');
        Route::get('/add', [SliderController::class, 'create']);
        Route::post('/add', [SliderController::class, 'store']);
        Route::get('/edit/{id}', [SliderController::class, 'edit']);
        Route::post('/edit/{id}', [SliderController::class, 'update']);
        Route::get('/delete/{id}', [SliderController::class, 'delete']);
    });

    // Master User
    Route::prefix('data/user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/add', [UserController::class, 'create']);
        Route::post('/add', [UserController::class, 'store']);
        Route::get('/edit/{id}', [UserController::class, 'edit']);
        Route::post('/edit/{id}', [UserController::class, 'update']);
        Route::get('/reset/{id}', [UserController::class, 'reset']);
        Route::get('/delete/{id}', [UserController::class, 'delete']);
    });

    // Master Satuan
    Route::prefix('data/satuan')->group(function () {
        Route::get('/', [SatuanController::class, 'index'])->name('satuan');
        Route::get('/add', [SatuanController::class, 'create']);
        Route::post('/add', [SatuanController::class, 'store']);
        Route::get('/edit/{id}', [SatuanController::class, 'edit']);
        Route::post('/edit/{id}', [SatuanController::class, 'update']);
        Route::get('/delete/{id}', [SatuanController::class, 'delete']);
    });

    // Master Kelompok
    Route::prefix('data/kelompok')->group(function () {
        Route::get('/', [KelompokController::class, 'index'])->name('kelompok');
        Route::get('/add', [KelompokController::class, 'create']);
        Route::post('/add', [KelompokController::class, 'store']);
        Route::get('/edit/{id}', [KelompokController::class, 'edit']);
        Route::post('/edit/{id}', [KelompokController::class, 'update']);
        Route::get('/delete/{id}', [KelompokController::class, 'delete']);
    });
    // Master Pasar
    Route::prefix('data/pasar')->group(function () {
        Route::get('/', [PasarController::class, 'index'])->name('pasar');
        Route::post('/add', [PasarController::class, 'store']);
        Route::post('/petugas', [PasarController::class, 'petugas']);
        Route::get('/{id}/hapus-petugas', [PasarController::class, 'hapusPetugas']);
        Route::get('/generateharga', [PasarController::class, 'generateHarga']);
        Route::get('/bapok/{id}', [PasarController::class, 'bapok']);
        Route::post('/bapok/{id}', [PasarController::class, 'updateBapok']);
        Route::get('/edit/{id}', [PasarController::class, 'edit']);
        Route::post('/edit/{id}', [PasarController::class, 'update']);
        Route::get('/delete/{id}', [PasarController::class, 'delete']);
        Route::get('/lokasi/{id}', [PasarController::class, 'lokasi']);
        Route::post('/lokasi/{id}', [PasarController::class, 'updateLokasi']);
        Route::get('/harga/{id}', [PasarController::class, 'harga']);
        Route::post('/harga/{id}/tahunbulan', [PasarController::class, 'bulanTahun']);
        Route::get('/harga/{id}/delete/{bulan_id}', [PasarController::class, 'deleteBulanTahun']);
        Route::get('/harga/{id}/acuan/{bulan_id}', [PasarController::class, 'acuan']);
        Route::post('/harga/{id}/acuan/{bulan_id}', [PasarController::class, 'storeAcuan']);
    });

    // Master Harga Acuan EWS
    Route::prefix('data/hargaacuan')->group(function () {
        Route::get('/', [HargaAcuanController::class, 'index'])->name('acuan');
        Route::post('/add', [HargaAcuanController::class, 'store']);
        Route::get('/bapok/{id}', [HargaAcuanController::class, 'hargaacuan']);
        Route::post('/bapok/{id}', [HargaAcuanController::class, 'storeHargaAcuan']);
        Route::get('/delete/{id}', [HargaAcuanController::class, 'delete']);
    });

    // Nomor Aduan
    Route::get('/data/nomoraduan', [AduanController::class, 'index'])->name('nomoraduan');
    Route::post('/data/nomoraduan', [AduanController::class, 'update']);

    // Notifikasi WhatsApp & EWS
    Route::get('/home/ews', [EWSController::class, 'ews']);
    Route::get('/home/wa-notifikasi', [NotifikasiController::class, 'kirimNotif']);
    Route::prefix('data/notifikasi')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('notifikasi');
        Route::get('/add', [NotifikasiController::class, 'create']);
        Route::post('/add', [NotifikasiController::class, 'store']);
        Route::get('/edit/{id}', [NotifikasiController::class, 'edit']);
        Route::post('/edit/{id}', [NotifikasiController::class, 'update']);
        Route::get('/delete/{id}', [NotifikasiController::class, 'delete']);
        Route::get('/kirim/{id}', [NotifikasiController::class, 'sendNotif']);
    });

    // Master Bahan
    Route::prefix('data/bahan')->group(function () {
        Route::get('/', [BahanController::class, 'index'])->name('bahan');
        Route::get('/add', [BahanController::class, 'create']);
        Route::post('/add', [BahanController::class, 'store']);
        Route::get('/edit/{id}', [BahanController::class, 'edit']);
        Route::post('/edit/{id}', [BahanController::class, 'update']);
        Route::get('/delete/{id}', [BahanController::class, 'delete']);
    });

    // Ganti Password
    Route::get('/data/gantipassword', [GantiPassController::class, 'index'])->name('gantipass');
    Route::post('/data/gantipassword', [GantiPassController::class, 'change']);

    // Input Harga
    Route::prefix('input/harga')->group(function () {
        Route::get('/', [HargaController::class, 'index'])->name('harga');
        Route::post('/update', [HargaController::class, 'updateService']);
        Route::get('/fulldate/{id}', [HargaController::class, 'fullDate']);
        Route::get('/{id}', [HargaController::class, 'pasar']);
        Route::get('/bahan/sekarang', [HargaController::class, 'storeHargaBahan']);
    });

    // Input Stok
    Route::prefix('input/stok')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('stok');
        Route::get('/month/{id}', [StokController::class, 'month']);
        Route::post('/update', [StokController::class, 'updateService']);
        Route::get('/{id}', [StokController::class, 'pasar']);
    });

    // Report
    Route::prefix('report')->group(function () {
        Route::get('/harga/rata-rata/harian', [ReportController::class, 'harian'])->name('report.harian');
        Route::get('/harga/rata-rata/harian/search', [ReportController::class, 'searchHarian']);
        Route::get('/harga/rata-rata/bulanan', [ReportController::class, 'bulanan'])->name('report.bulanan');
        Route::get('/harga/rata-rata/bulanan/search', [ReportController::class, 'searchBulanan']);
        Route::get('/harga/riwayat', [ReportController::class, 'riwayat'])->name('report.riwayat');
        Route::get('/harga/riwayat/search', [ReportController::class, 'riwayat']);
        Route::get('/stok/bulanan', [ReportController::class, 'stok'])->name('report.stokbulanan');
        Route::get('/stok/grafik', [ReportController::class, 'grafik_stok'])->name('report.grafik.stok');
        Route::get('/stok/grafik/search', [ReportController::class, 'search_grafik_stok']);
    });

});
