<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateHargaCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_existing_zero_price_with_previous_day_price(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar A']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-03',
            'harga' => 15000,
        ]);

        $existing = Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-04',
            'harga' => 0,
        ]);

        $this->artisan('generate:harga', [
            '--tanggal' => '2026-11-04',
            '--pasar_id' => $pasar->id,
            '--bahan_id' => $bahan->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('harga', [
            'id' => $existing->id,
            'tanggal' => '2026-11-04',
            'harga' => 15000,
        ]);
    }

    public function test_skips_when_existing_price_is_zero_and_previous_price_is_also_zero(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar A']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-03',
            'harga' => 0,
        ]);

        $existing = Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-04',
            'harga' => 0,
        ]);

        $this->artisan('generate:harga', [
            '--tanggal' => '2026-11-04',
            '--pasar_id' => $pasar->id,
            '--bahan_id' => $bahan->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('harga', [
            'id' => $existing->id,
            'tanggal' => '2026-11-04',
            'harga' => 0,
        ]);
    }

    public function test_skips_when_existing_price_is_zero_and_no_previous_price_exists(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar A']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        $existing = Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-04',
            'harga' => 0,
        ]);

        $this->artisan('generate:harga', [
            '--tanggal' => '2026-11-04',
            '--pasar_id' => $pasar->id,
            '--bahan_id' => $bahan->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('harga', [
            'id' => $existing->id,
            'tanggal' => '2026-11-04',
            'harga' => 0,
        ]);
    }

    public function test_skips_existing_non_zero_price_when_not_force(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar A']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-03',
            'harga' => 15000,
        ]);

        $existing = Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-04',
            'harga' => 16000,
        ]);

        $this->artisan('generate:harga', [
            '--tanggal' => '2026-11-04',
            '--pasar_id' => $pasar->id,
            '--bahan_id' => $bahan->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('harga', [
            'id' => $existing->id,
            'tanggal' => '2026-11-04',
            'harga' => 16000,
        ]);
    }

    public function test_does_not_update_when_existing_price_is_not_zero_even_with_force(): void
    {
        $pasar = Pasar::create(['nama' => 'Pasar A']);
        $bahan = Bahan::create(['nama' => 'Beras']);
        $pasar->bahan()->attach($bahan->id);

        Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-03',
            'harga' => 15000,
        ]);

        $existing = Harga::create([
            'pasar_id' => $pasar->id,
            'bahan_id' => $bahan->id,
            'tanggal' => '2026-11-04',
            'harga' => 16000,
        ]);

        $this->artisan('generate:harga', [
            '--tanggal' => '2026-11-04',
            '--pasar_id' => $pasar->id,
            '--bahan_id' => $bahan->id,
            '--force' => true,
        ])->assertSuccessful();

        // Harga tetap 16000 dan tidak di-update
        $this->assertDatabaseHas('harga', [
            'id' => $existing->id,
            'tanggal' => '2026-11-04',
            'harga' => 16000,
        ]);
    }
}
