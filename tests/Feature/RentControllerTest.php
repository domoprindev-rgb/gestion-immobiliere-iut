<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rent_page_generates_current_and_upcoming_installments_idempotently(): void
    {
        $property = Property::query()->create([
            'title' => 'Studio Jardin',
            'address' => 'Makepe, Douala',
            'price' => 95000,
            'type' => 'Studio',
            'status' => 'loue',
        ]);
        $tenant = Tenant::query()->create(['name' => 'Samuel Nguemo']);
        $property->leases()->create([
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'start_date' => today()->subMonths(3)->toDateString(),
            'rent_tracking_from' => today()->startOfMonth()->toDateString(),
            'rent_amount' => 95000,
            'due_day' => 5,
            'status' => 'actif',
        ]);

        $this->get(route('rents.index'))
            ->assertOk()
            ->assertSee('Samuel Nguemo')
            ->assertSee('Studio Jardin');

        $this->get(route('rents.index'))->assertOk();

        $this->assertDatabaseCount('rent_installments', 2);
    }
}
