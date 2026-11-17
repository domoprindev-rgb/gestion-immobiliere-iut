<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:nouvelle,en_cours,terminee'],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
        ]);

        return view('maintenance.index', [
            'requests' => MaintenanceRequest::query()
                ->with('property')
                ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
                ->when($filters['property_id'] ?? null, fn (Builder $query, int $propertyId): Builder => $query->where('property_id', $propertyId))
                ->orderByRaw("CASE status WHEN 'nouvelle' THEN 0 WHEN 'en_cours' THEN 1 ELSE 2 END")
                ->latest('reported_at')
                ->paginate(15)
                ->withQueryString(),
            'properties' => Property::query()->orderBy('title')->get(['id', 'title']),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'lease_id' => ['nullable', 'integer', 'exists:leases,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:3000'],
            'category' => ['required', 'string', 'max:80'],
            'priority' => ['required', Rule::in(['basse', 'normale', 'haute', 'urgente'])],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'reported_at' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $this->ensureLeaseBelongsToProperty($validated['lease_id'] ?? null, $validated['property_id']);

        MaintenanceRequest::query()->create([
            ...$validated,
            'reported_by' => $request->user()->id,
            'status' => 'nouvelle',
        ]);

        return to_route('maintenance.index')->with('success', 'La demande d’intervention a été créée.');
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['nouvelle', 'en_cours', 'terminee'])],
            'priority' => ['required', Rule::in(['basse', 'normale', 'haute', 'urgente'])],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $maintenanceRequest->update([
            ...$validated,
            'resolved_at' => $validated['status'] === 'terminee' ? today()->toDateString() : null,
        ]);

        return to_route('maintenance.index')->with('success', 'Le suivi de l’intervention a été mis à jour.');
    }

    private function ensureLeaseBelongsToProperty(?int $leaseId, int $propertyId): void
    {
        if ($leaseId !== null && ! Lease::query()->whereKey($leaseId)->where('property_id', $propertyId)->exists()) {
            throw ValidationException::withMessages([
                'lease_id' => 'Le bail sélectionné ne correspond pas au logement choisi.',
            ]);
        }
    }
}
