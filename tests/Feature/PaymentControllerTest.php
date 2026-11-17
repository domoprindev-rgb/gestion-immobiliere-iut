<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInstallment;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_recording_payment_creates_a_quittance_for_an_active_lease(): void
    {
        $lease = $this->createLease();
        $installment = $this->createInstallment($lease);

        $response = $this->post(route('payments.store'), [
            'rent_installment_id' => $installment->id,
            'amount' => 95000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'mobile_money',
        ]);

        $payment = Payment::query()->where('lease_id', $lease->id)->firstOrFail();
        $response->assertRedirect(route('payments.show', $payment))
            ->assertSessionHas('success');
        $this->assertMatchesRegularExpression('/^Q-\d{8}-[A-Z0-9]{6}$/', $payment->receipt_number);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'lease_id' => $lease->id,
            'rent_installment_id' => $installment->id,
            'amount' => 95000,
            'payment_method' => 'mobile_money',
        ]);
    }

    public function test_payment_cannot_be_recorded_for_a_terminated_lease(): void
    {
        $lease = $this->createLease(['status' => 'termine']);
        $installment = $this->createInstallment($lease);

        $response = $this->followingRedirects()
            ->from(route('properties.show', $lease->property))
            ->post(route('payments.store'), [
                'rent_installment_id' => $installment->id,
                'amount' => 95000,
                'payment_date' => today()->toDateString(),
                'payment_method' => 'especes',
            ]);

        $response->assertOk()
            ->assertSee('Cette échéance n’accepte plus de paiement.');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_cannot_exceed_the_remaining_installment_balance(): void
    {
        $lease = $this->createLease();
        $installment = $this->createInstallment($lease);
        $lease->payments()->create([
            'rent_installment_id' => $installment->id,
            'amount' => 50000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'virement',
            'receipt_number' => 'Q-TEST-PARTIAL',
        ]);

        $response = $this->from(route('rents.index'))
            ->post(route('payments.store'), [
                'rent_installment_id' => $installment->id,
                'amount' => 50001,
                'payment_date' => today()->toDateString(),
                'payment_method' => 'especes',
            ]);

        $response->assertRedirect(route('rents.index'))
            ->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_payment_receipt_can_be_downloaded_as_a_pdf(): void
    {
        $lease = $this->createLease();
        $installment = $this->createInstallment($lease);
        $payment = $lease->payments()->create([
            'rent_installment_id' => $installment->id,
            'amount' => 95000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'virement',
            'receipt_number' => 'Q-PDF-TEST',
        ]);

        $this->get(route('payments.receipt', $payment))
            ->assertDownload('quittance-Q-PDF-TEST.pdf');
    }

    public function test_payment_search_filters_by_tenant_and_payment_method(): void
    {
        $lease = $this->createLease();
        $matchingPayment = $lease->payments()->create([
            'amount' => 95000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'virement',
            'receipt_number' => 'Q-TEST-001',
        ]);
        $otherLease = $this->createLease([
            'tenant_name' => 'Samuel Nguemo',
            'tenant_email' => 'samuel@example.test',
        ]);
        $otherLease->payments()->create([
            'amount' => 85000,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'especes',
            'receipt_number' => 'Q-TEST-002',
        ]);

        $response = $this->get(route('payments.index', [
            'search' => 'Aïcha',
            'payment_method' => 'virement',
        ]));

        $response->assertOk()
            ->assertSee($matchingPayment->receipt_number)
            ->assertDontSee('Q-TEST-002');
    }

    private function createLease(array $attributes = []): Lease
    {
        $property = Property::query()->create([
            'title' => 'Appartement Paiement',
            'address' => 'Akwa, Douala',
            'price' => 95000,
            'type' => 'Studio',
            'status' => 'loue',
        ]);

        return $property->leases()->create([
            'tenant_id' => Tenant::query()->create([
                'name' => $attributes['tenant_name'] ?? 'Aïcha Mbarga',
                'email' => $attributes['tenant_email'] ?? 'aicha@example.test',
            ])->id,
            'tenant_name' => 'Aïcha Mbarga',
            'tenant_email' => 'aicha@example.test',
            'start_date' => today()->toDateString(),
            'rent_amount' => 95000,
            'status' => 'actif',
            'due_day' => 5,
            ...$attributes,
        ]);
    }

    private function createInstallment(Lease $lease): RentInstallment
    {
        return $lease->installments()->create([
            'period_start' => today()->startOfMonth()->toDateString(),
            'due_date' => today()->toDateString(),
            'amount_due' => 95000,
            'status' => 'open',
        ]);
    }
}
