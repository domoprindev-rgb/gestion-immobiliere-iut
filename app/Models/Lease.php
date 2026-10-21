<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    protected $fillable = ['property_id', 'tenant_name', 'tenant_email', 'start_date', 'end_date', 'rent_amount', 'status'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'rent_amount' => 'decimal:2'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
