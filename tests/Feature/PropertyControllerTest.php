<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_property_and_active_rent_totals(): void
    {
        $property = $this->createProperty(['status' => 'loue']);
        $lease = $property->leases()->create([
            'tenant_name' => 'Aïcha Mbarga',
            'tenant_email' => 'aicha@example.test',
            'start_date' => today()->toDateString(),
            'rent_amount' => 95000,
            'status' => 'actif',
        ]);
        $lease->payments()->create([
            'amount' => 95000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'virement',
            'receipt_number' => 'Q-TEST-DASHBOARD',
        ]);
        $this->createProperty(['title' => 'Villa libre', 'status' => 'disponible']);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('95 000')
            ->assertSee('Aïcha Mbarga')
            ->assertSee('Taux d’occupation');
    }

    public function test_property_search_and_status_filters_are_applied_together(): void
    {
        $this->createProperty(['title' => 'Appartement Bonapriso', 'status' => 'disponible']);
        $this->createProperty(['title' => 'Appartement Akwa', 'status' => 'loue']);
        $this->createProperty([
            'title' => 'Villa Akwa',
            'address' => 'Akwa, Douala',
            'type' => 'Maison',
            'status' => 'disponible',
        ]);

        $response = $this->get(route('properties.index', [
            'search' => 'Bonapriso',
            'status' => 'disponible',
        ]));

        $response->assertOk()
            ->assertSee('Appartement Bonapriso')
            ->assertDontSee('Appartement Akwa')
            ->assertDontSee('Villa Akwa');
    }

    public function test_property_updates_and_redirects_to_its_details(): void
    {
        $property = $this->createProperty();

        $response = $this->put(route('properties.update', $property), [
            'title' => 'Appartement rénové',
            'address' => 'Akwa, Douala',
            'price' => 125000,
            'type' => 'Appartement',
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Appartement rénové',
            'address' => 'Akwa, Douala',
            'price' => 125000,
        ]);
    }

    public function test_property_with_lease_history_cannot_be_deleted(): void
    {
        $property = $this->createProperty(['status' => 'loue']);
        $property->leases()->create([
            'tenant_name' => 'Locataire',
            'tenant_email' => 'locataire@example.test',
            'start_date' => today()->toDateString(),
            'rent_amount' => 95000,
            'status' => 'actif',
        ]);

        $response = $this->delete(route('properties.destroy', $property));

        $response->assertRedirect(route('properties.show', $property))
            ->assertSessionHas('error', 'Ce logement ne peut pas être supprimé car son historique contient un bail, une dépense ou une intervention.');
        $this->assertModelExists($property);
    }

    public function test_property_title_is_escaped_in_the_details_page(): void
    {
        $property = $this->createProperty(['title' => '<script>alert(1)</script>']);

        $response = $this->get(route('properties.show', $property));

        $response->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    private function createProperty(array $attributes = []): Property
    {
        return Property::query()->create([
            'title' => 'Appartement Test',
            'address' => 'Bonapriso, Douala',
            'price' => 95000,
            'type' => 'Studio',
            'status' => 'disponible',
            ...$attributes,
        ]);
    }
}
