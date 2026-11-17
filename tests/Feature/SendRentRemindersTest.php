<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Tenant;
use App\Notifications\RentPaymentReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendRentRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_pre_due_reminder_is_sent_only_once_for_an_unpaid_installment(): void
    {
        Notification::fake();
        $property = Property::query()->create([
            'title' => 'Duplex Bellevue',
            'address' => 'Akwa, Douala',
            'price' => 250000,
            'type' => 'Duplex',
            'status' => 'loue',
        ]);
        $tenant = Tenant::query()->create([
            'name' => 'Samuel Nguemo',
            'email' => 'samuel@example.test',
        ]);
        $lease = $property->leases()->create([
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'tenant_email' => $tenant->email,
            'start_date' => today()->toDateString(),
            'rent_amount' => 250000,
            'status' => 'actif',
            'due_day' => 5,
        ]);
        $installment = $lease->installments()->create([
            'period_start' => today()->startOfMonth()->toDateString(),
            'due_date' => today()->addDays(2)->toDateString(),
            'amount_due' => 250000,
            'status' => 'open',
        ]);

        $this->artisan('rents:send-reminders')->assertSuccessful();
        $this->artisan('rents:send-reminders')->assertSuccessful();

        Notification::assertSentOnDemand(
            RentPaymentReminder::class,
            fn (RentPaymentReminder $notification, array $channels, object $notifiable): bool => $notifiable->routes['mail'] === 'samuel@example.test',
        );
        Notification::assertSentOnDemandOnce(RentPaymentReminder::class);
        $this->assertNotNull($installment->fresh()->pre_reminded_at);
    }
}
