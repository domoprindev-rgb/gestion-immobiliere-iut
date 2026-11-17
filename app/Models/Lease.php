<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    protected $fillable = ['property_id', 'tenant_id', 'tenant_name', 'tenant_email', 'start_date', 'end_date', 'rent_amount', 'status', 'due_day', 'rent_tracking_from'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'rent_tracking_from' => 'date',
            'rent_amount' => 'decimal:2',
            'due_day' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(RentInstallment::class);
    }
}
