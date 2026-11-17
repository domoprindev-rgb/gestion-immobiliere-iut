<?php

namespace App\Http\Controllers;

use App\Models\RentInstallment;
use App\Services\RentInstallmentGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentController extends Controller
{
    public function index(Request $request, RentInstallmentGenerator $generator): View
    {
        $generator->generateForActiveLeases();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,due,overdue,paid'],
        ]);

        $installments = RentInstallment::query()
            ->with(['lease.property', 'lease.tenant'])
            ->withSum('payments as paid_amount', 'amount')
            ->where('status', 'open')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('lease', function (Builder $leaseQuery) use ($search): void {
                    $leaseQuery->where('tenant_name', 'like', "%{$search}%")
                        ->orWhereHas('property', fn (Builder $propertyQuery): Builder => $propertyQuery->where('title', 'like', "%{$search}%"));
                });
            })
            ->when(($filters['status'] ?? 'all') !== 'all', function (Builder $query) use ($filters): void {
                $paid = '(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.rent_installment_id = rent_installments.id)';

                match ($filters['status']) {
                    'paid' => $query->whereRaw("{$paid} >= rent_installments.amount_due"),
                    'overdue' => $query->whereDate('due_date', '<', today())->whereRaw("{$paid} < rent_installments.amount_due"),
                    'due' => $query->whereDate('due_date', '>=', today())->whereRaw("{$paid} < rent_installments.amount_due"),
                };
            })
            ->orderByDesc('due_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $allInstallments = RentInstallment::query()
            ->where('status', 'open')
            ->withSum('payments as paid_amount', 'amount')
            ->get();

        return view('rents.index', [
            'installments' => $installments,
            'expectedAmount' => $allInstallments->sum(fn (RentInstallment $installment): float => (float) $installment->amount_due),
            'collectedAmount' => $allInstallments->sum(fn (RentInstallment $installment): float => (float) ($installment->paid_amount ?? 0)),
            'overdueCount' => RentInstallment::query()
                ->where('status', 'open')
                ->whereDate('due_date', '<', today())
                ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.rent_installment_id = rent_installments.id) < rent_installments.amount_due')
                ->count(),
            'filters' => $filters,
        ]);
    }
}
