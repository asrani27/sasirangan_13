<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatHargaTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->role()->attach($adminRole->id);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('report.riwayat'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_riwayat_report_page(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('report.riwayat'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Perubahan Harga');
    }

    public function test_riwayat_report_filters_and_detects_price_changes_accurately(): void
    {
        $admin = $this->createAdminUser();

        $bahan = Bahan::create(['nama' => 'Bawang Merah']);
        $pasar = Pasar::create(['nama' => 'Pasar Pekauman']);

        // Seed consecutive prices:
        // Jan 02: 40,000 (initial baseline)
        // Jan 03: 40,000 (same price -> omitted)
        // Jan 05: 45,000 (up 5,000)
        // Jan 06: 45,000 (same price -> omitted)
        // Jan 10: 42,000 (down 3,000)
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
            'tanggal' => '2026-01-06',
            'harga' => 45000,
        ]);
        Harga::create([
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'tanggal' => '2026-01-10',
            'harga' => 42000,
        ]);

        $response = $this->actingAs($admin)->get(route('report.riwayat', [
            'bahan_id' => $bahan->id,
            'pasar_id' => $pasar->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]));

        $response->assertStatus(200);

        // View data assertions
        $riwayat = $response->viewData('riwayat');
        $this->assertCount(3, $riwayat);

        // Sort is descending (newest first)
        $this->assertSame('2026-01-10', $riwayat[0]['tanggal']);
        $this->assertSame(42000.0, (float) $riwayat[0]['harga']);
        $this->assertSame('down', $riwayat[0]['status']);
        $this->assertSame(-3000.0, (float) $riwayat[0]['selisih']);

        $this->assertSame('2026-01-05', $riwayat[1]['tanggal']);
        $this->assertSame(45000.0, (float) $riwayat[1]['harga']);
        $this->assertSame('up', $riwayat[1]['status']);
        $this->assertSame(5000.0, (float) $riwayat[1]['selisih']);

        $this->assertSame('2026-01-02', $riwayat[2]['tanggal']);
        $this->assertSame(40000.0, (float) $riwayat[2]['harga']);
        $this->assertSame('awal', $riwayat[2]['status']);

        // Assert rendered texts in view
        $response->assertSee('up 5.000');
        $response->assertSee('down 3.000');
        $response->assertSee('Data Awal');
    }

    public function test_sidebar_includes_riwayat_perubahan_harga_link(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin);

        $view = $this->view('layouts.partials.admin-sidebar');
        $view->assertSee(route('report.riwayat'));
        $view->assertSee('Riwayat Perubahan Harga');
    }
}
