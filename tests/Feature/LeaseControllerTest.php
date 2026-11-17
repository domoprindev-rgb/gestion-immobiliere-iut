<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_lease_marks_the_property_as_occupied(): void
    {
        $property = $this->createProperty();
        $tenant = $this->createTenant();

        $response = $this->post(route('leases.store'), [
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => today()->toDateString(),
            'end_date' => today()->addYear()->toDateString(),
            'rent_amount' => 110000,
            'due_day' => 5,
        ]);

        $response->assertRedirect(route('properties.show', $property))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('leases', [
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'tenant_email' => $tenant->email,
            'rent_amount' => 110000,
            'status' => 'actif',
        ]);
        $this->assertSame('loue', $property->fresh()->status);
    }

    public function test_a_second_active_lease_is_rejected_for_an_occupied_property(): void
    {
        $property = $this->createProperty(['status' => 'loue']);
        $this->createLease($property);
        $tenant = $this->createTenant('Samuel Nguemo', 'samuel@example.test');

        $response = $this->followingRedirects()
            ->from(route('properties.show', $property))
            ->post(route('leases.store'), [
                'property_id' => $property->id,
                'tenant_id' => $tenant->id,
                'start_date' => today()->toDateString(),
                'rent_amount' => 110000,
                'due_day' => 5,
            ]);

        $response->assertOk()
            ->assertSee('Ce logement est déjà loué.');
        $this->assertDatabaseCount('leases', 1);
    }

    public function test_future_start_date_is_rejected_for_an_active_lease(): void
    {
        $property = $this->createProperty();
        $tenant = $this->createTenant();

        $response = $this->followingRedirects()
            ->from(route('properties.show', $property))
            ->post(route('leases.store'), [
                'property_id' => $property->id,
                'tenant_id' => $tenant->id,
                'start_date' => today()->addDay()->toDateString(),
                'rent_amount' => 110000,
                'due_day' => 5,
            ]);

        $response->assertOk()
            ->assertSee('La date de début ne peut pas être postérieure à aujourd’hui.');
        $this->assertDatabaseCount('leases', 0);
        $this->assertSame('disponible', $property->fresh()->status);
    }

    public function test_active_lease_details_can_be_updated(): void
    {
        $property = $this->createProperty(['status' => 'loue']);
        $lease = $this->createLease($property);
        $tenant = $this->createTenant('Aïcha Mbarga Ndiaye', 'aicha.ndiaye@example.test');
        $lease->installments()->create([
            'period_start' => today()->startOfMonth()->toDateString(),
            'due_date' => today()->startOfMonth()->addDays(4)->toDateString(),
            'amount_due' => 110000,
            'status' => 'open',
        ]);

        $response = $this->put(route('leases.update', $lease), [
            'tenant_id' => $tenant->id,
            'start_date' => today()->toDateString(),
            'end_date' => today()->addYear()->toDateString(),
            'rent_amount' => 125000,
            'due_day' => 7,
        ]);

        $response->assertRedirect(route('properties.show', $property))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'tenant_name' => 'Aïcha Mbarga Ndiaye',
            'tenant_email' => 'aicha.ndiaye@example.test',
            'rent_amount' => 125000,
            'due_day' => 7,
        ]);
        $this->assertDatabaseHas('rent_installments', [
            'lease_id' => $lease->id,
            'amount_due' => 125000,
            'due_date' => today()->startOfMonth()->addDays(6)->toDateTimeString(),
        ]);
    }

    public function test_terminating_a_lease_releases_its_property_and_keeps_history(): void
    {
        $property = $this->createProperty(['status' => 'loue']);
        $lease = $this->createLease($property);
        $futureInstallment = $lease->installments()->create([
            'period_start' => today()->startOfMonth()->addMonthNoOverflow()->toDateString(),
            'due_date' => today()->addMonthNoOverflow()->toDateString(),
            'amount_due' => 110000,
            'status' => 'open',
        ]);

        $response = $this->post(route('leases.terminate', $lease));

        $response->assertRedirect(route('leases.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'status' => 'termine',
        ]);
        $this->assertSame(today()->toDateString(), $lease->fresh()->end_date->toDateString());
        $this->assertSame('disponible', $property->fresh()->status);
        $this->assertDatabaseHas('rent_installments', [
            'id' => $futureInstallment->id,
            'status' => 'annulee',
        ]);
    }

    private function createProperty(array $attributes = []): Property
    {
        return Property::query()->create([
            'title' => 'Appartement Test',
            'address' => 'Bonapriso, Douala',
            'price' => 110000,
            'type' => 'Appartement',
            'status' => 'disponible',
            ...$attributes,
        ]);
    }

    private function createLease(Property $property, array $attributes = []): Lease
    {
        return $property->leases()->create([
            'tenant_id' => $this->createTenant()->id,
            'tenant_name' => 'Aïcha Mbarga',
            'tenant_email' => 'aicha@example.test',
            'start_date' => today()->toDateString(),
            'rent_amount' => 110000,
            'status' => 'actif',
            'due_day' => 5,
            ...$attributes,
        ]);
    }

    private function createTenant(string $name = 'Aïcha Mbarga', string $email = 'aicha@example.test'): Tenant
    {
        return Tenant::query()->create([
            'name' => $name,
            'email' => $email,
        ]);
    }
}
