<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInstallment;
use App\Models\Tenant;
use App\Services\RentInstallmentGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:disponible,loue'],
        ]);

        $propertiesQuery = Property::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status));

        return view('properties.index', [
            'properties' => $propertiesQuery->latest()->orderByDesc('id')->paginate(10)->withQueryString(),
            'propertyCount' => Property::count(),
            'availableCount' => Property::where('status', 'disponible')->count(),
            'occupiedCount' => Property::where('status', 'loue')->count(),
        ]);
    }

    public function dashboard(): View
    {
        $monthStart = now()->startOfMonth();
        $chartStart = now()->startOfMonth()->subMonths(5);
        $paymentsByMonth = Payment::query()
            ->whereDate('payment_date', '>=', $chartStart->toDateString())
            ->whereDate('payment_date', '<=', now()->toDateString())
            ->get(['payment_date', 'amount'])
            ->groupBy(fn (Payment $payment): string => $payment->payment_date->format('Y-m'))
            ->map(fn ($payments): float => (float) $payments->sum('amount'));

        $monthlyIncome = collect(range(5, 0))
            ->map(function (int $monthsAgo) use ($paymentsByMonth): array {
                $month = now()->startOfMonth()->subMonths($monthsAgo);

                return [
                    'label' => $month->locale('fr')->translatedFormat('M'),
                    'amount' => $paymentsByMonth->get($month->format('Y-m'), 0),
                ];
            });

        return view('welcome', [
            'propertyCount' => Property::count(),
            'occupiedCount' => Property::where('status', 'loue')->count(),
            'availableCount' => Property::where('status', 'disponible')->count(),
            'activeLeaseCount' => Lease::where('status', 'actif')->count(),
            'expectedMonthlyRent' => Lease::where('status', 'actif')->sum('rent_amount'),
            'overdueInstallmentCount' => RentInstallment::query()
                ->where('status', 'open')
                ->whereDate('due_date', '<', today())
                ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.rent_installment_id = rent_installments.id) < rent_installments.amount_due')
                ->count(),
            'collectedThisMonth' => Payment::query()
                ->whereDate('payment_date', '>=', $monthStart->toDateString())
                ->whereDate('payment_date', '<=', now()->toDateString())
                ->sum('amount'),
            'monthlyIncome' => $monthlyIncome,
            'monthlyIncomeMax' => max((float) $monthlyIncome->max('amount'), 1),
            'recentPayments' => Payment::query()->with('lease.property')->latest('payment_date')->limit(6)->get(),
            'expiringLeases' => Lease::query()
                ->with('property')
                ->where('status', 'actif')
                ->whereNotNull('end_date')
                ->whereDate('end_date', '>=', today()->toDateString())
                ->whereDate('end_date', '<=', today()->addDays(30)->toDateString())
                ->orderBy('end_date')
                ->limit(4)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'max:80'],
        ]);

        Property::create($validated);

        return to_route('properties.index')->with('success', 'Le logement a été ajouté.');
    }

    public function edit(Property $property): View
    {
        return view('properties.edit', [
            'property' => $property->loadCount(['leases', 'expenses', 'maintenanceRequests']),
        ]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'max:80'],
        ]);

        $property->update($validated);

        return to_route('properties.show', $property)->with('success', 'La fiche du logement a été mise à jour.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        if ($property->leases()->exists() || $property->expenses()->exists() || $property->maintenanceRequests()->exists()) {
            return to_route('properties.show', $property)
                ->with('error', 'Ce logement ne peut pas être supprimé car son historique contient un bail, une dépense ou une intervention.');
        }

        $property->delete();

        return to_route('properties.index')->with('success', 'Le logement a été supprimé.');
    }

    public function show(Property $property, RentInstallmentGenerator $generator): View
    {
        $property->leases()
            ->where('status', 'actif')
            ->get()
            ->each(fn (Lease $lease) => $generator->generateForLease($lease));

        return view('properties.show', [
            'property' => $property->load([
                'leases' => fn ($query) => $query->latest(),
                'leases.tenant',
                'leases.payments' => fn ($query) => $query->latest('payment_date'),
                'leases.payments.installment',
                'leases.installments' => fn ($query) => $query->orderByDesc('period_start')->limit(6)->withSum('payments as paid_amount', 'amount'),
            ]),
            'tenants' => Tenant::query()->orderBy('name')->get(),
        ]);
    }
}
