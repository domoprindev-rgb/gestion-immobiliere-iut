<?php

namespace App\Console\Commands;

use App\Models\RentInstallment;
use App\Notifications\RentPaymentReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('rents:send-reminders')]
#[Description('Email tenants about upcoming and overdue rent installments')]
class SendRentReminders extends Command
{
    public function handle(): int
    {
        $sent = 0;

        RentInstallment::query()
            ->with(['lease.tenant', 'lease.property'])
            ->withSum('payments as paid_amount', 'amount')
            ->where('status', 'open')
            ->whereDate('due_date', '<=', today()->addDays(3)->toDateString())
            ->whereHas('lease', fn ($query) => $query->where('status', 'actif'))
            ->orderBy('due_date')
            ->chunkById(100, function ($installments) use (&$sent): void {
                foreach ($installments as $installment) {
                    if ($installment->outstanding_amount <= 0 || ! $installment->lease->tenant?->email) {
                        continue;
                    }

                    $isOverdue = $installment->due_date->lt(today());

                    if ($isOverdue && $installment->overdue_reminded_at !== null) {
                        continue;
                    }

                    if (! $isOverdue && $installment->pre_reminded_at !== null) {
                        continue;
                    }

                    Notification::route('mail', $installment->lease->tenant->email)
                        ->notify(new RentPaymentReminder($installment, $installment->outstanding_amount));

                    $installment->update([
                        $isOverdue ? 'overdue_reminded_at' : 'pre_reminded_at' => now(),
                    ]);
                    $sent++;
                }
            });

        $this->info("{$sent} rappel(s) envoyé(s).");

        return self::SUCCESS;
    }
}
