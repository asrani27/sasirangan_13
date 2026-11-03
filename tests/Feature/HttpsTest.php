<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HttpsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_contains_upgrade_insecure_requests_meta(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">', false);
    }

    public function test_admin_layout_contains_upgrade_insecure_requests_meta(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->role()->attach($adminRole->id);

        $response = $this->actingAs($admin)->get(route('report.riwayat'));
        $response->assertStatus(200);
        $response->assertSee('<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">', false);
    }

    public function test_trusted_proxy_headers_are_respected(): void
    {
        $response = $this->get('/', [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_HOST' => 'dedikasi.banjarmasinkota.go.id',
        ]);

        $response->assertStatus(200);
    }

    public function test_force_scheme_produces_https_urls(): void
    {
        URL::forceScheme('https');
        $assetUrl = asset('build/assets/app.css');

        $this->assertStringStartsWith('https://', $assetUrl);
    }
}
