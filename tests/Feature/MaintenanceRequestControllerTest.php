<?php

namespace Tests\Feature;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_request_can_be_created_and_marked_complete(): void
    {
        $property = Property::query()->create([
            'title' => 'Villa des Manguiers',
            'address' => 'Bonamoussadi, Douala',
            'price' => 320000,
            'type' => 'Maison',
            'status' => 'loue',
        ]);

        $response = $this->post(route('maintenance.store'), [
            'property_id' => $property->id,
            'title' => 'Fuite dans la cuisine',
            'description' => 'Le siphon sous l’évier fuit.',
            'category' => 'Plomberie',
            'priority' => 'haute',
            'estimated_cost' => 25000,
            'reported_at' => today()->toDateString(),
        ]);
        $maintenanceRequest = MaintenanceRequest::query()->firstOrFail();

        $response->assertRedirect(route('maintenance.index'))
            ->assertSessionHas('success');
        $this->put(route('maintenance.update', $maintenanceRequest), [
            'status' => 'terminee',
            'priority' => 'normale',
            'estimated_cost' => 25000,
            'actual_cost' => 22500,
        ])->assertRedirect(route('maintenance.index'));

        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $maintenanceRequest->id,
            'status' => 'terminee',
            'actual_cost' => 22500,
        ]);
        $this->assertSame(today()->toDateString(), $maintenanceRequest->fresh()->resolved_at->toDateString());
        $this->get(route('maintenance.index'))
            ->assertOk()
            ->assertSee('Fuite dans la cuisine');
    }
}
