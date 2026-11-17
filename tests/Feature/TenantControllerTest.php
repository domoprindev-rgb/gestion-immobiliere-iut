<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_be_created_and_viewed_from_its_record(): void
    {
        $response = $this->post(route('tenants.store'), [
            'name' => 'Aïcha Mbarga',
            'email' => 'aicha@example.test',
            'phone' => '+237600000000',
        ]);
        $tenant = Tenant::query()->where('email', 'aicha@example.test')->firstOrFail();

        $response->assertRedirect(route('tenants.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Aïcha Mbarga',
            'phone' => '+237600000000',
        ]);
        $this->get(route('tenants.show', $tenant))
            ->assertOk()
            ->assertSee('Aïcha Mbarga');
    }

    public function test_accountant_cannot_create_a_tenant_record(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'comptable']));

        $this->post(route('tenants.store'), [
            'name' => 'Aïcha Mbarga',
        ])->assertForbidden();

        $this->assertDatabaseCount('tenants', 0);
    }
}
