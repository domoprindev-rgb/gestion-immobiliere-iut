<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_page_summarizes_rent_receipts_and_expenses_for_the_period(): void
    {
        $property = Property::query()->create([
            'title' => 'Résidence Les Palmiers',
            'address' => 'Bonapriso, Douala',
            'price' => 95000,
            'type' => 'Appartement',
            'status' => 'loue',
        ]);
        $tenant = Tenant::query()->create(['name' => 'Mireille Fotso']);
        $lease = $property->leases()->create([
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'start_date' => today()->toDateString(),
            'rent_amount' => 95000,
            'status' => 'actif',
            'due_day' => 5,
            'rent_tracking_from' => today()->startOfMonth()->toDateString(),
        ]);
        $installment = $lease->installments()->create([
            'period_start' => today()->startOfMonth()->toDateString(),
            'due_date' => today()->toDateString(),
            'amount_due' => 95000,
            'status' => 'open',
        ]);
        $lease->payments()->create([
            'rent_installment_id' => $installment->id,
            'amount' => 95000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'virement',
            'receipt_number' => 'Q-REPORT-001',
        ]);
        Expense::query()->create([
            'property_id' => $property->id,
            'category' => 'Entretien',
            'description' => 'Nettoyage des parties communes',
            'amount' => 10000,
            'expense_date' => today()->toDateString(),
        ]);

        $this->get(route('reports.index', [
            'from' => today()->startOfMonth()->toDateString(),
            'to' => today()->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('Mireille Fotso')
            ->assertSee('Q-REPORT-001')
            ->assertSee('Nettoyage des parties communes')
            ->assertSee('95 000')
            ->assertSee('10 000');
    }

    public function test_report_can_be_exported_as_a_csv_for_the_selected_period(): void
    {
        $response = $this->get(route('reports.export', [
            'from' => today()->startOfMonth()->toDateString(),
            'to' => today()->toDateString(),
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload('rapport-'.today()->startOfMonth()->toDateString().'-'.today()->toDateString().'.csv');
    }
}
