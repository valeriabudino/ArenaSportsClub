<?php

namespace Tests\Feature;

use App\Models\AccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncAccessLogsTest extends TestCase
{
    use RefreshDatabase;

    private function fakeReport(): array
    {
        return [
            'date' => '2026-07-06',
            'total_scans' => 2,
            'approved' => 1,
            'rejected' => 1,
            'logs' => [
                [
                    'timestamp' => '2026-07-06 14:30:00',
                    'qr_code' => 'RESERVA-12345',
                    'status' => 'approved',
                    'details' => 'Usuario de Canchas (QR)',
                ],
                [
                    'timestamp' => '2026-07-06 15:45:00',
                    'qr_code' => 'RESERVA-VENCIDA',
                    'status' => 'rejected',
                    'details' => 'Intento Invalido (QR)',
                ],
            ],
        ];
    }

    public function test_syncs_logs_from_the_report(): void
    {
        Http::fake([
            '*/api/canchas/daily-logs*' => Http::response($this->fakeReport(), 200),
        ]);

        $this->artisan('accesos:sincronizar', ['--date' => '2026-07-06'])->assertSuccessful();

        $this->assertDatabaseCount('access_logs', 2);
        $this->assertDatabaseHas('access_logs', ['qr_code' => 'RESERVA-12345', 'status' => 'approved']);
        $this->assertDatabaseHas('access_logs', ['qr_code' => 'RESERVA-VENCIDA', 'status' => 'rejected']);
    }

    public function test_does_not_duplicate_logs_already_synced(): void
    {
        Http::fake([
            '*/api/canchas/daily-logs*' => Http::response($this->fakeReport(), 200),
        ]);

        $this->artisan('accesos:sincronizar', ['--date' => '2026-07-06'])->assertSuccessful();
        $this->artisan('accesos:sincronizar', ['--date' => '2026-07-06'])->assertSuccessful();

        $this->assertDatabaseCount('access_logs', 2);
    }

    public function test_fails_gracefully_when_the_api_is_unreachable(): void
    {
        Http::fake([
            '*/api/canchas/daily-logs*' => Http::response(null, 500),
        ]);

        $this->artisan('accesos:sincronizar')->assertFailed();

        $this->assertDatabaseCount('access_logs', 0);
    }

    public function test_admin_can_view_the_access_logs_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        AccessLog::create([
            'date' => now()->toDateString(),
            'scanned_at' => now(),
            'qr_code' => 'RESERVA-12345',
            'status' => 'approved',
            'details' => 'Usuario de Canchas (QR)',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.access-logs.index'));

        $response->assertOk();
        $response->assertSee('RESERVA-12345');
    }

    public function test_a_regular_user_cannot_view_the_access_logs_report(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.access-logs.index'))->assertForbidden();
    }
}
