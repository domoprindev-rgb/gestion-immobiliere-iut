<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $properties = collect([
            [
                'title' => 'Résidence Les Palmiers',
                'address' => 'Bonapriso, Douala',
                'price' => 185000,
                'type' => 'Appartement',
            ],
            [
                'title' => 'Studio Jardin',
                'address' => 'Makepe, Douala',
                'price' => 95000,
                'type' => 'Studio',
            ],
            [
                'title' => 'Villa des Manguiers',
                'address' => 'Bonamoussadi, Douala',
                'price' => 320000,
                'type' => 'Maison',
            ],
            [
                'title' => 'Duplex Bellevue',
                'address' => 'Akwa, Douala',
                'price' => 250000,
                'type' => 'Duplex',
            ],
        ]);

        $tenants = [
            ['name' => 'Aïcha Mbarga', 'email' => 'aicha.mbarga@example.test'],
            ['name' => 'Samuel Nguemo', 'email' => 'samuel.nguemo@example.test'],
            ['name' => 'Mireille Fotso', 'email' => 'mireille.fotso@example.test'],
        ];

        foreach ($properties as $index => $attributes) {
            $property = Property::firstOrCreate(
                ['title' => $attributes['title']],
                [...$attributes, 'status' => $index < 3 ? 'loue' : 'disponible'],
            );

            if ($index >= 3) {
                continue;
            }

            $tenant = $tenants[$index];
            $tenantRecord = Tenant::query()->firstOrCreate(
                ['email' => $tenant['email']],
                ['name' => $tenant['name']],
            );
            $lease = Lease::firstOrCreate(
                ['property_id' => $property->id, 'tenant_email' => $tenant['email']],
                [
                    'tenant_id' => $tenantRecord->id,
                    'tenant_name' => $tenant['name'],
                    'start_date' => now()->subMonths(8)->startOfMonth()->toDateString(),
                    'end_date' => now()->addMonths(4)->endOfMonth()->toDateString(),
                    'rent_amount' => $attributes['price'],
                    'status' => 'actif',
                    'due_day' => 5,
                    'rent_tracking_from' => now()->startOfMonth()->toDateString(),
                ],
            );

            $property->update(['status' => 'loue']);

            foreach (range(0, 5) as $monthOffset) {
                $paymentMonth = now()->startOfMonth()->subMonths(5 - $monthOffset);
                $paymentDate = $paymentMonth->copy()->addDays(4);
                if ($paymentDate->isAfter(today())) {
                    $paymentDate = today();
                }
                $paymentMethods = ['virement', 'mobile_money', 'especes'];

                Payment::firstOrCreate(
                    ['receipt_number' => 'DEMO-'.$lease->id.'-'.$paymentMonth->format('Ym')],
                    [
                        'lease_id' => $lease->id,
                        'amount' => $attributes['price'],
                        'payment_date' => $paymentDate->toDateString(),
                        'payment_method' => $paymentMethods[$monthOffset % count($paymentMethods)],
                    ],
                );
            }
        }
    }
}
