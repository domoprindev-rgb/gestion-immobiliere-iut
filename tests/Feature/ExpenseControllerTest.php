<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_is_saved_and_listed_for_its_property(): void
    {
        $property = Property::query()->create([
            'title' => 'Appartement Bonapriso',
            'address' => 'Bonapriso, Douala',
            'price' => 150000,
            'type' => 'Appartement',
            'status' => 'disponible',
        ]);

        $response = $this->post(route('expenses.store'), [
            'property_id' => $property->id,
            'category' => 'Réparation',
            'description' => 'Remplacement du robinet',
            'amount' => 12500,
            'expense_date' => today()->toDateString(),
            'vendor' => 'Plomberie Express',
        ]);
        $expense = Expense::query()->firstOrFail();

        $response->assertRedirect(route('expenses.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'property_id' => $property->id,
            'amount' => 12500,
            'created_by' => auth()->id(),
        ]);
        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertSee('Remplacement du robinet')
            ->assertSee('12 500');
    }
}
