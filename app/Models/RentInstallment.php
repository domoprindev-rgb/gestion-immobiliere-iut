<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentInstallment extends Model
{
    protected $fillable = [
        'lease_id',
        'period_start',
        'due_date',
        'amount_due',
        'pre_reminded_at',
        'overdue_reminded_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'due_date' => 'date',
            'amount_due' => 'decimal:2',
            'pre_reminded_at' => 'datetime',
            'overdue_reminded_at' => 'datetime',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max((float) $this->amount_due - (float) ($this->paid_amount ?? 0), 0);
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'annulee') {
            return 'Annulée';
        }

        if ($this->outstanding_amount <= 0) {
            return 'Payée';
        }

        if ((float) ($this->paid_amount ?? 0) > 0) {
            return 'Partielle';
        }

        return $this->due_date->lt(today()) ? 'En retard' : 'À payer';
    }
}
