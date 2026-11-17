<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Services\RentInstallmentGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:actif,termine,tous'],
        ]);

        $leases = Lease::query()
            ->with('property')
            ->withCount('payments')
            ->when(($filters['status'] ?? 'actif') !== 'tous', fn (Builder $query): Builder => $query->where('status', $filters['status'] ?? 'actif'))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('tenant_name', 'like', "%{$search}%")
                        ->orWhere('tenant_email', 'like', "%{$search}%")
                        ->orWhereHas('property', fn (Builder $propertyQuery): Builder => $propertyQuery->where('title', 'like', "%{$search}%"));
                });
            })
            ->orderBy('status')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('leases.index', [
            'leases' => $leases,
            'activeLeaseCount' => Lease::where('status', 'actif')->count(),
            'endedLeaseCount' => Lease::where('status', 'termine')->count(),
            'activeRentAmount' => Lease::where('status', 'actif')->sum('rent_amount'),
        ]);
    }

    public function store(Request $request, RentInstallmentGenerator $generator): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'after_or_equal:today'],
            'rent_amount' => ['required', 'numeric', 'min:1'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ], [
            'start_date.before_or_equal' => 'La date de début ne peut pas être postérieure à aujourd’hui.',
        ]);

        $property = DB::transaction(function () use ($validated, $generator): Property {
            $property = Property::query()->lockForUpdate()->findOrFail($validated['property_id']);

            if ($property->status !== 'disponible' || $property->leases()->where('status', 'actif')->exists()) {
                throw ValidationException::withMessages([
                    'property_id' => 'Ce logement est déjà loué.',
                ]);
            }

            $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
            $trackingFrom = Carbon::parse($validated['start_date'])
                ->startOfMonth()
                ->max(now()->startOfMonth())
                ->toDateString();
            $lease = $property->leases()->create([
                ...$validated,
                'tenant_name' => $tenant->name,
                'tenant_email' => $tenant->email,
                'rent_tracking_from' => $trackingFrom,
            ]);
            $property->update(['status' => 'loue']);
            $generator->generateForLease($lease);

            return $property;
        });

        return to_route('properties.show', $property)->with('success', 'Le bail a été créé. Vous pouvez maintenant enregistrer les loyers.');
    }

    public function edit(Lease $lease): View
    {
        abort_unless($lease->status === 'actif', 404);

        return view('leases.edit', [
            'lease' => $lease->load('property', 'tenant'),
            'tenants' => Tenant::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Lease $lease, RentInstallmentGenerator $generator): RedirectResponse
    {
        abort_unless($lease->status === 'actif', 404);

        $validated = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'after_or_equal:today'],
            'rent_amount' => ['required', 'numeric', 'min:1'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ], [
            'start_date.before_or_equal' => 'La date de début ne peut pas être postérieure à aujourd’hui.',
        ]);

        $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
        $lease->update([
            ...$validated,
            'tenant_name' => $tenant->name,
            'tenant_email' => $tenant->email,
        ]);
        $generator->generateForLease($lease->fresh());

        return to_route('properties.show', $lease->property_id)->with('success', 'Les informations du bail ont été mises à jour.');
    }

    public function terminate(Lease $lease): RedirectResponse
    {
        DB::transaction(function () use ($lease): void {
            $lease = Lease::query()->lockForUpdate()->findOrFail($lease->id);

            if ($lease->status !== 'actif') {
                throw ValidationException::withMessages([
                    'lease' => 'Ce bail est déjà terminé.',
                ]);
            }

            $lease->update([
                'status' => 'termine',
                'end_date' => today()->toDateString(),
            ]);
            $lease->installments()
                ->where('status', 'open')
                ->whereDate('due_date', '>', today())
                ->whereDoesntHave('payments')
                ->update(['status' => 'annulee']);

            $property = Property::query()->lockForUpdate()->findOrFail($lease->property_id);

            if (! $property->leases()->where('status', 'actif')->exists()) {
                $property->update(['status' => 'disponible']);
            }
        });

        return to_route('leases.index')->with('success', 'Le bail a été clôturé et le logement est de nouveau disponible.');
    }
}
