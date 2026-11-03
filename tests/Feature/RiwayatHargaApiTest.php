<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use App\Models\Satuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatHargaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_riwayat_harga_api_returns_success_and_valid_structure(): void
    {
        $satuan = Satuan::create(['nama' => 'Kg']);
        $bahan = Bahan::create(['nama' => 'Bawang Merah', 'satuan_id' => $satuan->id]);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);

        // Baseline on Jan 02: 40.000
        // Same price on Jan 03: 40.000 (omitted)
        // Naik on Jan 05: 45.000 (+5.000)
        // Turun on Jan 10: 42.000 (-3.000)
        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-01-02',
            'harga' => 40000,
        ]);
        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-01-03',
            'harga' => 40000,
        ]);
        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-01-05',
            'harga' => 45000,
        ]);
        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-01-10',
            'harga' => 42000,
        ]);

        $response = $this->getJson(route('api.info-harga.riwayat', [
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'bahan' => ['id', 'nama', 'satuan'],
                'pasar' => ['id', 'nama'],
                'periode' => ['start_date', 'end_date'],
                'ringkasan' => [
                    'harga_awal',
                    'harga_terkini',
                    'total_perubahan',
                    'total_naik',
                    'total_turun',
                ],
                'total_data',
                'log_format',
                'data' => [
                    '*' => [
                        'tanggal',
                        'tanggal_formatted',
                        'harga',
                        'harga_formatted',
                        'prev_harga',
                        'prev_harga_formatted',
                        'selisih',
                        'selisih_formatted',
                        'persen',
                        'persen_formatted',
                        'status',
                        'keterangan',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertSame(3, $data['total_data']);
        $this->assertSame(1, $data['ringkasan']['total_naik']);
        $this->assertSame(1, $data['ringkasan']['total_turun']);
        $this->assertSame(2, $data['ringkasan']['total_perubahan']);

        // Check log format strings
        $this->assertContains('tanggal 10 jan 2026 : 42.000 (down 3.000)', $data['log_format']);
        $this->assertContains('tanggal 5 jan 2026 : 45.000 (up 5.000)', $data['log_format']);
        $this->assertContains('tanggal 2 jan 2026 : 40.000', $data['log_format']);
    }

    public function test_riwayat_harga_alias_endpoint_works(): void
    {
        $bahan = Bahan::create(['nama' => 'Bawang Merah']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);

        $response = $this->getJson(route('api.riwayat-harga', [
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data Riwayat Perubahan Harga Ditemukan',
            ]);
    }

    public function test_riwayat_harga_supports_indonesian_param_aliases(): void
    {
        $bahan = Bahan::create(['nama' => 'Bawang Merah']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);

        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-02-01',
            'harga' => 30000,
        ]);

        $response = $this->getJson(route('api.info-harga.riwayat', [
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal_mulai' => '2026-02-01',
            'tanggal_berakhir' => '2026-02-28',
        ]));

        $response->assertStatus(200);
        $this->assertSame('2026-02-01', $response->json('periode.start_date'));
        $this->assertSame('2026-02-28', $response->json('periode.end_date'));
        $this->assertSame(1, $response->json('total_data'));
    }

    public function test_riwayat_harga_validates_invalid_input(): void
    {
        $response = $this->getJson(route('api.info-harga.riwayat', [
            'bahan_id' => 99999,
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['bahan_id']);
    }
}
