<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100']]);

        return view('tenants.index', [
            'tenants' => Tenant::query()
                ->withCount('leases')
                ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Tenant::query()->create($validated);

        return to_route('tenants.index')->with('success', 'Le dossier du locataire a été créé.');
    }

    public function show(Tenant $tenant): View
    {
        return view('tenants.show', [
            'tenant' => $tenant->load(['leases.property', 'leases.payments' => fn ($query) => $query->latest('payment_date')]),
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', ['tenant' => $tenant]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($tenant, $validated): void {
            $tenant->update($validated);
            $tenant->leases()->update([
                'tenant_name' => $tenant->name,
                'tenant_email' => $tenant->email,
            ]);
        });

        return to_route('tenants.show', $tenant)->with('success', 'Le dossier du locataire a été mis à jour.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        if ($tenant->leases()->exists()) {
            return to_route('tenants.show', $tenant)->with('error', 'Ce locataire ne peut pas être supprimé car son dossier contient des baux.');
        }

        $tenant->delete();

        return to_route('tenants.index')->with('success', 'Le dossier du locataire a été supprimé.');
    }
}
