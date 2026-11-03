<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_has_confirmation_on_logout(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->role()->attach($adminRole->id);

        $this->actingAs($user);

        $view = $this->view('layouts.partials.admin-sidebar');

        $view->assertSee(route('logout'));
        $view->assertSee("onsubmit=\"return confirm('Apakah Anda yakin ingin keluar?')\"", false);
        $view->assertSee('DEDIKASI');
        $view->assertDontSee('SASIRANGAN');
    }
}
