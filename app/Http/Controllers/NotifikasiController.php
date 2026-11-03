<?php

namespace App\Http\Controllers;

use App\Models\Kenaikan;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function sendNotif(int|string $id): RedirectResponse
    {
        $notif = Notifikasi::findOrFail($id);
        $pesan = [
            'sendTo' => $notif->nomor,
            'content' => [
                'text' => Carbon::now()->translatedFormat('d F Y').
                    " \nEarly Warning system (EWS), \n Harga Bahan Pokok Yang mengalami Kenaikan : \n Link : ".url('/kenaikan')." \n",
            ],
        ];

        try {
            Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer IjF-1UCnC-Vf0iOkYU568oKL0YY_FET7tSCmh5VsnIZeuB6Pyy9_QvP0Uc5z0Z7amQG8s6uTecVSS1czxgBcF2IzQxfROxsgHHtGnRH1bjVXUpPuuZX8C9KkB8GsJFFZ0_R3qsLntIhfdR_V-RlFo-rCI2U9sjDk-Q5QqI6pe_efBaEGAx4ZuCp6sCAIIhO29grFy2gkTwbd0ZLJabhM4UPLKJihkrvebmhGR3e6BOrWBEsEqIni11yFNSOZnhbG5Hwy0S84kgah03QGo9pLEhw3HTR4tLQ8VWy2EMn_6u6QwF6jnTln8I_ddFCgn4cvkvPmh464cEzUUvHbIlRuow',
            ])->post('https://whatsapp.banjarmasinkota.go.id/api/service/send', $pesan);

            toastr()->success('Berhasil Di Kirim');
        } catch (\Throwable $e) {
            toastr()->error('Gagal mengirim WhatsApp: '.$e->getMessage());
        }

        return back();
    }

    public function kirimNotif(): RedirectResponse
    {
        $today = Carbon::today()->format('Y-m-d');
        $bapokNaik = Kenaikan::with(['bahan', 'pasar'])
            ->where('tanggal', $today)
            ->get();

        if ($bapokNaik->isEmpty()) {
            toastr()->info('Tidak ada kenaikan harga hari ini untuk dikirim.');

            return back();
        }

        $bapokLines = [];
        foreach ($bapokNaik as $key => $item) {
            $bapokLines[] = ($key + 1).'. '.($item->bahan?->nama ?? '-').' ('.($item->pasar?->nama ?? '-').') +'.round($item->kenaikan, 1).'%';
        }

        $textMessage = Carbon::now()->translatedFormat('d F Y')."\nEarly Warning System (EWS)\nHarga Bahan Pokok Yang Mengalami Kenaikan:\n".
            implode("\n", $bapokLines)."\n\nLink: ".url('/kenaikan');

        $numbers = Notifikasi::all();
        if ($numbers->isEmpty()) {
            toastr()->error('Tidak ada nomor penerima di daftar notifikasi');

            return back();
        }

        foreach ($numbers as $n) {
            try {
                Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6ImUzODVmODJiLWI5ZmMtNDdlYy05MWEwLWJkZDkzYzQ5Njc2NyIsImlhdCI6MTY5NTYxODU0M30.ks-dSXCKtB-aeigPwFZVPJ4b9gP_QculLQmw25Ypey4',
                ])->post('https://api.wa.banjarmasinkota.go.id/whatsapp/e385f82b-b9fc-47ec-91a0-bdd93c496767/messages', [
                    'phoneNumber' => $n->nomor,
                    'content' => ['text' => $textMessage],
                ]);
            } catch (\Throwable $e) {
                // Continue to next number
            }
        }

        toastr()->success('Berhasil Di Kirim ke '.$numbers->count().' nomor');

        return back();
    }

    public function index(): View
    {
        $data = Notifikasi::orderBy('id', 'DESC')->paginate(10);

        return view('admin.notifikasi.index', compact('data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nomor' => 'required|string']);

        $check = Notifikasi::where('nomor', $request->nomor)->first();
        if ($check != null) {
            toastr()->error('Nomor Sudah Ada');

            return back();
        }

        Notifikasi::create($request->all());
        toastr()->success('Nomor Berhasil Di Simpan');

        return back();
    }

    public function edit(int|string $id): View
    {
        $data = Notifikasi::orderBy('id', 'DESC')->paginate(10);
        $edit = Notifikasi::findOrFail($id);

        return view('admin.notifikasi.edit', compact('data', 'edit'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate(['nomor' => 'required|string']);

        $data = Notifikasi::findOrFail($id);
        $data->nomor = $request->nomor;
        $data->save();

        toastr()->success('Nomor Berhasil Di Update');

        return redirect('/data/notifikasi');
    }

    public function delete(int|string $id): RedirectResponse
    {
        Notifikasi::findOrFail($id)->delete();
        toastr()->success('Nomor Berhasil Di Hapus');

        return redirect('/data/notifikasi');
    }
}
