<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use App\Models\Satuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerubahanHargaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_perubahan_harga_api_returns_only_commodities_with_price_changes_and_only_last_change(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);

        $bahanA = Bahan::create(['nama' => 'Bawang Merah', 'satuan_id' => $satuanKg->id]);
        $bahanB = Bahan::create(['nama' => 'Beras Medium', 'satuan_id' => $satuanKg->id]);
        $bahanC = Bahan::create(['nama' => 'Cabai Rawit', 'satuan_id' => $satuanKg->id]);

        $pasar->bahan()->attach([$bahanA->id, $bahanB->id, $bahanC->id]);

        // Bahan A: 2x perubahan (Jan 02: 40k, Jan 05: 45k, Jan 10: 42k)
        Harga::create(['bahan_id' => $bahanA->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-02', 'harga' => 40000]);
        Harga::create(['bahan_id' => $bahanA->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-05', 'harga' => 45000]);
        Harga::create(['bahan_id' => $bahanA->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-10', 'harga' => 42000]);

        // Bahan B: konstan (Jan 02: 15k, Jan 05: 15k, Jan 10: 15k)
        Harga::create(['bahan_id' => $bahanB->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-02', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahanB->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-05', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahanB->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-10', 'harga' => 15000]);

        // Bahan C: 1x perubahan (Jan 02: 50k, Jan 08: 55k)
        Harga::create(['bahan_id' => $bahanC->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-02', 'harga' => 50000]);
        Harga::create(['bahan_id' => $bahanC->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-08', 'harga' => 55000]);

        $response = $this->getJson(route('api.perubahan-harga', [
            'pasar_id' => $pasar->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'pasar' => ['id', 'nama'],
                'ringkasan' => ['total_bahan_berubah', 'total_naik', 'total_turun'],
                'total_data',
                'log_format',
                'data' => [
                    '*' => [
                        'bahan_id', 'bahan_nama', 'satuan',
                        'bahan' => ['id', 'nama', 'satuan'],
                        'tanggal', 'tanggal_formatted',
                        'harga', 'harga_formatted',
                        'prev_harga', 'prev_harga_formatted',
                        'selisih', 'selisih_formatted',
                        'persen', 'persen_formatted',
                        'status', 'keterangan',
                    ],
                ],
            ]);

        $payload = $response->json();
        $this->assertTrue($payload['success']);
        $this->assertSame(2, $payload['total_data']);
        $this->assertSame(2, $payload['ringkasan']['total_bahan_berubah']);
        $this->assertSame(1, $payload['ringkasan']['total_naik']);
        $this->assertSame(1, $payload['ringkasan']['total_turun']);

        $bahanIds = collect($payload['data'])->pluck('bahan_id')->all();
        $this->assertContains($bahanA->id, $bahanIds);
        $this->assertContains($bahanC->id, $bahanIds);
        $this->assertNotContains($bahanB->id, $bahanIds);

        // Bahan A: hanya perubahan terakhir (Jan 10)
        $dataBahanA = collect($payload['data'])->firstWhere('bahan_id', $bahanA->id);
        $this->assertSame('2026-01-10', $dataBahanA['tanggal']);
        $this->assertSame(42000, $dataBahanA['harga']);
        $this->assertSame(45000, $dataBahanA['prev_harga']);
        $this->assertSame(-3000, $dataBahanA['selisih']);
        $this->assertSame('down', $dataBahanA['status']);

        // Bahan C: perubahan terakhir (Jan 08)
        $dataBahanC = collect($payload['data'])->firstWhere('bahan_id', $bahanC->id);
        $this->assertSame('2026-01-08', $dataBahanC['tanggal']);
        $this->assertSame(55000, $dataBahanC['harga']);
        $this->assertSame(50000, $dataBahanC['prev_harga']);
        $this->assertSame(5000, $dataBahanC['selisih']);
        $this->assertSame('up', $dataBahanC['status']);
    }

    public function test_perubahan_harga_api_with_default_pasar(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Bawang Merah']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-01', 'harga' => 30000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-02', 'harga' => 35000]);

        $response = $this->getJson(route('api.perubahan-harga'));

        $response->assertStatus(200);
        $this->assertSame($pasar->id, $response->json('pasar.id'));
        $this->assertSame(1, $response->json('total_data'));
    }

    public function test_perubahan_harga_api_alias_endpoint_works(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Bawang Merah']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-01', 'harga' => 30000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-02', 'harga' => 35000]);

        $response = $this->getJson(route('api.riwayat-harga-terakhir', [
            'pasar_id' => $pasar->id,
        ]));

        $response->assertStatus(200);
        $this->assertSame(1, $response->json('total_data'));
    }

    public function test_perubahan_harga_validates_invalid_pasar_id(): void
    {
        $response = $this->getJson(route('api.perubahan-harga', [
            'pasar_id' => 99999,
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pasar_id']);
    }

    public function test_perubahan_harga_empty_when_no_price_changes(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar Baru']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-01-01', 'harga' => 15000]);

        $response = $this->getJson(route('api.perubahan-harga', [
            'pasar_id' => $pasar->id,
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_data' => 0,
                'data' => [],
            ]);
    }

    public function test_perubahan_harga_api_filtered_by_bahan_id_returns_latest_price_change_per_pasar(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $bahan = Bahan::create(['nama' => 'Cabai Rawit', 'satuan_id' => $satuanKg->id]);

        $pasarA = Pasar::create(['nama' => 'Pasar Teluk Dalam']);
        $pasarB = Pasar::create(['nama' => 'Pasar Lama']);
        $pasarC = Pasar::create(['nama' => 'Pasar Antasari']);

        $bahan->pasar()->attach([$pasarA->id, $pasarB->id, $pasarC->id]);

        // Pasar A: 2x perubahan (Jan 02: 40k, Jan 05: 45k, Jan 10: 42k)
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarA->id, 'tanggal' => '2026-01-02', 'harga' => 40000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarA->id, 'tanggal' => '2026-01-05', 'harga' => 45000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarA->id, 'tanggal' => '2026-01-10', 'harga' => 42000]);

        // Pasar B: konstan (Jan 02: 50k, Jan 05: 50k)
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarB->id, 'tanggal' => '2026-01-02', 'harga' => 50000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarB->id, 'tanggal' => '2026-01-05', 'harga' => 50000]);

        // Pasar C: 1x perubahan (Jan 02: 30k, Jan 08: 35k)
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarC->id, 'tanggal' => '2026-01-02', 'harga' => 30000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarC->id, 'tanggal' => '2026-01-08', 'harga' => 35000]);

        $response = $this->getJson(route('api.riwayat-harga-terakhir', [
            'bahan_id' => $bahan->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'bahan' => ['id', 'nama', 'satuan'],
                'ringkasan' => ['total_pasar_berubah', 'total_naik', 'total_turun'],
                'total_pasar',
                'total_data',
                'log_format',
                'data' => [
                    '*' => [
                        'pasar_id', 'pasar_nama',
                        'pasar' => ['id', 'nama'],
                        'bahan_id', 'bahan_nama', 'satuan',
                        'bahan' => ['id', 'nama', 'satuan'],
                        'tanggal', 'tanggal_formatted',
                        'harga', 'harga_formatted',
                        'prev_harga', 'prev_harga_formatted',
                        'selisih', 'selisih_formatted',
                        'persen', 'persen_formatted',
                        'status', 'keterangan',
                    ],
                ],
            ]);

        $payload = $response->json();
        $this->assertTrue($payload['success']);
        $this->assertSame($bahan->id, $payload['bahan']['id']);
        $this->assertSame('Cabai Rawit', $payload['bahan']['nama']);
        $this->assertSame(2, $payload['total_data']);
        $this->assertSame(2, $payload['ringkasan']['total_pasar_berubah']);
        $this->assertSame(1, $payload['ringkasan']['total_naik']);
        $this->assertSame(1, $payload['ringkasan']['total_turun']);

        $pasarIds = collect($payload['data'])->pluck('pasar_id')->all();
        $this->assertContains($pasarA->id, $pasarIds);
        $this->assertContains($pasarC->id, $pasarIds);
        $this->assertNotContains($pasarB->id, $pasarIds);

        // Pasar A: hanya perubahan terakhir (Jan 10)
        $dataPasarA = collect($payload['data'])->firstWhere('pasar_id', $pasarA->id);
        $this->assertSame('2026-01-10', $dataPasarA['tanggal']);
        $this->assertSame(42000, $dataPasarA['harga']);
        $this->assertSame(45000, $dataPasarA['prev_harga']);
        $this->assertSame(-3000, $dataPasarA['selisih']);
        $this->assertSame('down', $dataPasarA['status']);

        // Pasar C: perubahan terakhir (Jan 08)
        $dataPasarC = collect($payload['data'])->firstWhere('pasar_id', $pasarC->id);
        $this->assertSame('2026-01-08', $dataPasarC['tanggal']);
        $this->assertSame(35000, $dataPasarC['harga']);
        $this->assertSame(30000, $dataPasarC['prev_harga']);
        $this->assertSame(5000, $dataPasarC['selisih']);
        $this->assertSame('up', $dataPasarC['status']);
    }

    public function test_perubahan_harga_api_filtered_by_bahan_id_and_pasar_id(): void
    {
        $bahan = Bahan::create(['nama' => 'Cabai Rawit']);
        $pasarA = Pasar::create(['nama' => 'Pasar Teluk Dalam']);
        $pasarB = Pasar::create(['nama' => 'Pasar Antasari']);

        $bahan->pasar()->attach([$pasarA->id, $pasarB->id]);

        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarA->id, 'tanggal' => '2026-01-01', 'harga' => 30000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarA->id, 'tanggal' => '2026-01-05', 'harga' => 35000]);

        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarB->id, 'tanggal' => '2026-01-01', 'harga' => 40000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasarB->id, 'tanggal' => '2026-01-05', 'harga' => 45000]);

        $response = $this->getJson(route('api.riwayat-harga-terakhir', [
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasarA->id,
        ]));

        $response->assertStatus(200);
        $this->assertSame(1, $response->json('total_data'));
        $this->assertSame($pasarA->id, $response->json('data.0.pasar_id'));
    }

    public function test_perubahan_harga_validates_invalid_bahan_id(): void
    {
        $response = $this->getJson(route('api.riwayat-harga-terakhir', [
            'bahan_id' => 99999,
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['bahan_id']);
    }
}
