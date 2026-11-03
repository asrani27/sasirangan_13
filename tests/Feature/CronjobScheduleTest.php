<?php

namespace Tests\Feature;

use App\Models\Cronjob;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CronjobScheduleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test bahwa tugas terjadwal terdaftar dengan benar di scheduler.
     */
    public function test_scheduled_tasks_are_registered_with_correct_frequency(): void
    {
        $schedule = $this->app->make(Schedule::class);
        $events = collect($schedule->events());

        // Verifikasi command ews dijadwalkan pada 13:45 setiap hari
        $ewsEvent = $events->first(function (Event $event) {
            return str_contains($event->command ?? '', 'ews');
        });
        $this->assertNotNull($ewsEvent, 'Command ews harus terdaftar dalam jadwal');
        $this->assertSame('45 13 * * *', $ewsEvent->expression);

        // Verifikasi command generate:harga dijadwalkan pada 01:10 setiap hari
        $hargaEvent = $events->first(function (Event $event) {
            return str_contains($event->command ?? '', 'generate:harga');
        });
        $this->assertNotNull($hargaEvent, 'Command generate:harga harus terdaftar dalam jadwal');
        $this->assertSame('10 1 * * *', $hargaEvent->expression);

        // Verifikasi command model:prune dijadwalkan setiap hari
        $pruneEvent = $events->first(function (Event $event) {
            return str_contains($event->command ?? '', 'model:prune');
        });
        $this->assertNotNull($pruneEvent, 'Command model:prune harus terdaftar dalam jadwal');
        $this->assertSame('0 0 * * *', $pruneEvent->expression);
    }

    /**
     * Test bahwa Cronjob model melakukan pruning data yang lebih lama dari 30 hari.
     */
    public function test_cronjob_prunes_records_older_than_30_days(): void
    {
        // Buat data log 40 hari lalu dan data log hari ini
        Cronjob::create(['cronjob' => now()->subDays(40)->format('Y-m-d H:i:s')]);
        Cronjob::create(['cronjob' => now()->format('Y-m-d H:i:s')]);

        $this->assertSame(2, Cronjob::count());

        $this->artisan('model:prune', ['--model' => [Cronjob::class]])->assertSuccessful();

        $this->assertSame(1, Cronjob::count());
        $remaining = Cronjob::first();
        $this->assertTrue(now()->parse($remaining->cronjob)->isToday());
    }

    /**
     * Test bahwa command ews mencatat 1 data ke tabel cronjob saat dieksekusi.
     */
    public function test_ews_command_records_to_cronjob_table(): void
    {
        $this->assertSame(0, Cronjob::count());

        $this->artisan('ews')->assertSuccessful();

        $this->assertSame(1, Cronjob::count());
    }
}
