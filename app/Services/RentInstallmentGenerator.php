<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\RentInstallment;
use Illuminate\Support\Carbon;

class RentInstallmentGenerator
{
    public function generateForLease(Lease $lease, ?Carbon $through = null): int
    {
        if ($lease->status !== 'actif') {
            return 0;
        }

        $period = Carbon::parse($lease->rent_tracking_from ?? $lease->start_date)->startOfMonth();
        $lastPeriod = ($through ?? now())->copy()->startOfMonth()->addMonthNoOverflow();
        $endMonth = $lease->end_date?->copy()->startOfMonth();
        $created = 0;

        while ($period->lessThanOrEqualTo($lastPeriod)
            && ($endMonth === null || $period->lessThanOrEqualTo($endMonth))) {
            $dueDate = $period->copy()->day(min($lease->due_day ?? 5, $period->daysInMonth));
            $installment = RentInstallment::query()->firstOrCreate(
                [
                    'lease_id' => $lease->id,
                    'period_start' => $period->copy(),
                ],
                [
                    'due_date' => $dueDate->toDateString(),
                    'amount_due' => $lease->rent_amount,
                    'status' => 'open',
                ],
            );

            if ($installment->wasRecentlyCreated) {
                $created++;
            } elseif ($installment->status === 'open'
                && $period->greaterThanOrEqualTo(now()->startOfMonth())
                && ! $installment->payments()->exists()) {
                $installment->update([
                    'due_date' => $dueDate->toDateString(),
                    'amount_due' => $lease->rent_amount,
                ]);
            }

            $period->addMonthNoOverflow();
        }

        return $created;
    }

    public function generateForActiveLeases(): int
    {
        $created = 0;

        Lease::query()
            ->where('status', 'actif')
            ->whereNotNull('rent_tracking_from')
            ->chunkById(100, function ($leases) use (&$created): void {
                foreach ($leases as $lease) {
                    $created += $this->generateForLease($lease);
                }
            });

        return $created;
    }
}
