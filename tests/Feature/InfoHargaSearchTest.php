<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use App\Models\Satuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfoHargaSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_previous_different_price_when_price_rose(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Beras Medium', 'satuan_id' => $satuanKg->id]);
        $pasar->bahan()->attach($bahan->id);

        // 4 Nov: 15.000, 3 Nov: 15.000, 2 Nov: 15.000, 1 Nov: 12.000
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-01', 'harga' => 12000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-02', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-03', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-04', 'harga' => 15000]);

        $response = $this->get(route('info-harga.search', [
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-11-04',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Harga Sebelum');
        $response->assertDontSee('Harga Kemarin');

        // Should display 12.000 (1 Nov 2026) for previous price
        $response->assertSee('Rp 12.000');
        $response->assertSee('(1 Nov 2026)');

        // Today's price
        $response->assertSee('Rp 15.000');

        // Difference: +Rp 3.000
        $response->assertSee('+Rp 3.000');

        // Status: Naik (+25.0%)
        $response->assertSee('Naik (+25.0%)');
    }

    public function test_displays_previous_different_price_when_price_dropped(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Cabai Rawit', 'satuan_id' => $satuanKg->id]);
        $pasar->bahan()->attach($bahan->id);

        // 4 Nov: 12.000, 3 Nov: 12.000, 2 Nov: 15.000
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-02', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-03', 'harga' => 12000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-04', 'harga' => 12000]);

        $response = $this->get(route('info-harga.search', [
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-11-04',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Harga Sebelum');

        // Should display 15.000 (2 Nov 2026)
        $response->assertSee('Rp 15.000');
        $response->assertSee('(2 Nov 2026)');

        // Today's price
        $response->assertSee('Rp 12.000');

        // Difference: -Rp 3.000
        $response->assertSee('-Rp 3.000');

        // Status: Turun (-20.0%)
        $response->assertSee('Turun (-20.0%)');
    }

    public function test_displays_stabil_when_price_never_changed_in_history(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Gula Pasir', 'satuan_id' => $satuanKg->id]);
        $pasar->bahan()->attach($bahan->id);

        // 4 Nov: 15.000, 3 Nov: 15.000
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-03', 'harga' => 15000]);
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-04', 'harga' => 15000]);

        $response = $this->get(route('info-harga.search', [
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-11-04',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Harga Sebelum');
        $response->assertSee('(3 Nov 2026)');
        $response->assertSee('Rp 0');
        $response->assertSee('Stabil');
    }

    public function test_displays_stabil_when_no_prior_price_exists(): void
    {
        $satuanKg = Satuan::create(['nama' => 'Kg']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);
        $bahan = Bahan::create(['nama' => 'Minyak Goreng', 'satuan_id' => $satuanKg->id]);
        $pasar->bahan()->attach($bahan->id);

        // Only 4 Nov: 20.000 (no previous entries)
        Harga::create(['bahan_id' => $bahan->id, 'pasar_id' => $pasar->id, 'tanggal' => '2026-11-04', 'harga' => 20000]);

        $response = $this->get(route('info-harga.search', [
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-11-04',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Harga Sebelum');
        $response->assertSee('Rp 20.000');
        $response->assertSee('Stabil');
    }
}
