<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_see_info_stok_and_grafik_stok_in_header(): void
    {
        $view = $this->view('layouts.partials.header');

        $view->assertDontSee(route('info-stok'));
        $view->assertDontSee(route('grafik.stok'));
        $view->assertDontSee('Info Stok');
        $view->assertDontSee('Grafik Stok');
        $view->assertDontSee(route('kenaikan'));
        $view->assertDontSee('EWS / Kenaikan');
        $view->assertDontSee('Peringatan EWS');
        $view->assertSee(route('info-harga'));
        $view->assertSee(route('grafik.harga'));
        $view->assertSee('DEDIKASI');
        $view->assertDontSee('SASIRANGAN');
    }

    public function test_admin_can_see_info_stok_and_grafik_stok_in_header(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->role()->attach($adminRole->id);

        $this->actingAs($user);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isSuperAdmin());

        $view = $this->view('layouts.partials.header');

        $view->assertSee(route('info-stok'));
        $view->assertSee(route('grafik.stok'));
        $view->assertSee('Info Stok');
        $view->assertSee('Grafik Stok');
        $view->assertDontSee(route('kenaikan'));
        $view->assertDontSee('EWS / Kenaikan');
        $view->assertDontSee('Peringatan EWS');
    }

    public function test_superadmin_can_see_info_stok_and_grafik_stok_in_header(): void
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $user = User::factory()->create();
        $user->role()->attach($superadminRole->id);

        $this->actingAs($user);

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isSuperAdmin());

        $view = $this->view('layouts.partials.header');

        $view->assertSee(route('info-stok'));
        $view->assertSee(route('grafik.stok'));
        $view->assertSee('Info Stok');
        $view->assertSee('Grafik Stok');
        $view->assertSee(route('kenaikan'));
        $view->assertSee('EWS / Kenaikan');
        $view->assertSee('Peringatan EWS');
    }

    public function test_petugas_cannot_see_info_stok_and_grafik_stok_in_header(): void
    {
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);
        $user = User::factory()->create();
        $user->role()->attach($petugasRole->id);

        $this->actingAs($user);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSuperAdmin());

        $view = $this->view('layouts.partials.header');

        $view->assertDontSee(route('info-stok'));
        $view->assertDontSee(route('grafik.stok'));
        $view->assertDontSee('Info Stok');
        $view->assertDontSee('Grafik Stok');
        $view->assertDontSee(route('kenaikan'));
        $view->assertDontSee('EWS / Kenaikan');
        $view->assertDontSee('Peringatan EWS');
    }
}
